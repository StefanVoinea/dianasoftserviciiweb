# Se uita daca pe ecran sta deschisa o fereastra de PIN, si atat.
#
# Cand legatura cu ANAF cade, pricina cea mai deasa nu e reteaua: e fereastra
# de PIN a tokenului, deschisa pe calculatorul clientului si asteptand pe cineva
# care nu se uita acolo. Din aplicatie, pana acum, asta arata la fel cu un
# server picat — omul cauta vina in retea, iar tokenul astepta cuminte.
#
# Aici nu se atinge nimic: se numara ferestrele deschise si se spune ce s-a
# vazut. PIN-ul se scrie tot de mana, de omul care tine tokenul — cheia lui nu
# trece prin nicio aplicatie si prin niciun server.
#
# Proba nu deschide nicio fereastra, spre deosebire de pin-test.ps1, care o
# forteaza dinadins ca sa afle daca PIN-ul e dat. Aici se cauta doar ce e deja
# acolo, deci se poate chema oricand, si dupa fiecare pana.

$ErrorActionPreference = 'Stop'

[Console]::OutputEncoding = New-Object System.Text.UTF8Encoding $false

function Raspunde($date) {
    ($date | ConvertTo-Json -Compress)
    exit 0
}

function EDialog($clasa) {
    <#
        „#32770" e clasa dialogurilor Win32 — acolo stau cele mai multe cereri
        de PIN. Programele de token scrise mai nou, in WinForms sau WPF, au insa
        alta clasa, si ar fi trecut nevazute.
    #>
    return ($clasa -eq '#32770') -or ($clasa -like 'WindowsForms*') -or ($clasa -like 'HwndWrapper*')
}


# Programele care cer PIN-ul tokenului. Numele difera de la un furnizor la
# altul, si de aceea se cauta si dupa ce scrie pe fereastra, nu doar dupa ele.
$Programe = @(
    'SACSrv', 'SafeNetAuthenticationClient', 'eToken', 'eTSrv', 'eTBase',
    'iDProtect', 'bit4id', 'bit4xpki', 'AWP', 'certSIGN', 'DigiSign',
    'TokenAdmin', 'CryptoIdeMngr', 'ClassicClient', 'Gclib', 'SmartCardService',
    'dkck', 'CardOS', 'SecureStoreCSP'
)

# Ce scrie pe fereastra unei cereri de PIN, in romana si in engleza.
$Vorbe = 'PIN|Token|Smart ?Card|Autentificare|Authentication|Log ?[Oo]n|Introduce|Enter'

try {
    if (-not ('DianaSoft.Ferestre' -as [type])) {
        Add-Type -Namespace DianaSoft -Name Ferestre -MemberDefinition @'
[DllImport("user32.dll")]
public static extern bool EnumWindows(EnumWindowsProc lpEnumFunc, IntPtr lParam);

public delegate bool EnumWindowsProc(IntPtr hWnd, IntPtr lParam);

[DllImport("user32.dll")]
public static extern bool IsWindowVisible(IntPtr hWnd);

[DllImport("user32.dll", CharSet = CharSet.Unicode)]
public static extern int GetWindowTextW(IntPtr hWnd, System.Text.StringBuilder lpString, int nMaxCount);

[DllImport("user32.dll", CharSet = CharSet.Unicode)]
public static extern int GetClassNameW(IntPtr hWnd, System.Text.StringBuilder lpClassName, int nMaxCount);

[DllImport("user32.dll")]
public static extern int GetWindowThreadProcessId(IntPtr hWnd, out int lpdwProcessId);
'@
    }

    $gasite = New-Object System.Collections.ArrayList

    $culegatorul = [DianaSoft.Ferestre+EnumWindowsProc] {
        param($fereastra, $nefolosit)

        if (-not [DianaSoft.Ferestre]::IsWindowVisible($fereastra)) {
            return $true
        }

        $titlu = New-Object System.Text.StringBuilder 512
        [void][DianaSoft.Ferestre]::GetWindowTextW($fereastra, $titlu, 512)
        $titlu = $titlu.ToString().Trim()

        if ($titlu -eq '') {
            return $true
        }

        $clasa = New-Object System.Text.StringBuilder 256
        [void][DianaSoft.Ferestre]::GetClassNameW($fereastra, $clasa, 256)
        $clasa = $clasa.ToString()

        $idProces = 0
        [void][DianaSoft.Ferestre]::GetWindowThreadProcessId($fereastra, [ref]$idProces)

        $proces = ''

        try {
            $proces = (Get-Process -Id $idProces -ErrorAction Stop).ProcessName
        } catch {
            $proces = ''
        }

        # Ori programul e unul cunoscut, ori pe fereastra scrie despre PIN. A
        # doua cale prinde furnizorii pe care nu-i știm încă pe nume.
        $alNostru = $Programe -contains $proces
        $spuneDePin = $titlu -match $Vorbe

        $eDialog = EDialog $clasa

        if ($alNostru -or ($eDialog -and $spuneDePin)) {
            [void]$gasite.Add([ordered]@{
                titlu  = $titlu
                proces = $proces
                clasa  = $clasa
            })
        }

        return $true
    }

    [void][DianaSoft.Ferestre]::EnumWindows($culegatorul, [IntPtr]::Zero)

    if ($gasite.Count -eq 0) {
        Raspunde ([ordered]@{
            deschisa = $false
            titlu    = ''
            proces   = ''
        })
    }

    $cea = $gasite[0]

    Raspunde ([ordered]@{
        deschisa = $true
        titlu    = $cea.titlu
        proces   = $cea.proces
        clasa    = $cea.clasa
        cate     = $gasite.Count
    })
} catch {
    <#
        Nereușita probei nu înseamnă că nu e nicio fereastră — înseamnă doar că
        n-am putut afla. Se spune așa, ca aplicația să nu dea vina pe token când
        vina e a probei.
    #>
    Raspunde ([ordered]@{
        deschisa = $false
        titlu    = ''
        proces   = ''
        eroare   = $_.Exception.Message
    })
}
