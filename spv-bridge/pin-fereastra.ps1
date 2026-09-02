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
#
# Cu „-Toate" se spun toate ferestrele vazute, potrivite sau nu: asa se afla,
# cand un furnizor nou nu e recunoscut, ce scrie pe fereastra lui si cine a
# deschis-o.
param(
    [switch]$Toate
)

$ErrorActionPreference = 'Stop'

[Console]::OutputEncoding = New-Object System.Text.UTF8Encoding $false

function Raspunde($date) {
    ($date | ConvertTo-Json -Compress -Depth 4)
    exit 0
}

<#
    Programele care cer PIN-ul tokenului.

    Numele difera de la un furnizor la altul, si de multe ori fereastra nici nu
    e a lor: driverul o deschide chiar in programul care a cerut cheia — curl,
    php, oricare. De aceea lista aceasta e doar una dintre cai; a doua, si cea
    care prinde furnizorii necunoscuti, e ce scrie pe fereastra.
#>
$Programe = @(
    'SACSrv', 'SACUI', 'SACMonitor', 'SafeNetAuthenticationClient',
    'eToken', 'eTSrv', 'eTBase', 'eTMonitor',
    'iDProtect', 'bit4id', 'bit4xpki', 'AWP', 'certSIGN', 'DigiSign',
    'TokenAdmin', 'CryptoIdeMngr', 'ClassicClient', 'Gclib', 'SmartCardService',
    'dkck', 'CardOS', 'SecureStoreCSP'
)

<#
    Ce scrie pe fereastra unei cereri de PIN, in romana si in engleza.

    „Token Logon" e chiar titlul ferestrei SafeNet Authentication Client, cea
    prin care trec certificatele certSIGN.
#>
$Vorbe = 'PIN|Token Logon|Token Password|Parola token|Smart ?Card|Autentificare|Authentication|Log ?[Oo]n|Introduce[tț]i'

<#
    Ferestrele mari de aplicatie, care nu sunt niciodata cereri de PIN.

    Fara ele, un titlu de browser cu „Authentication" in el ar fi luat drept
    fereastra tokenului — iar in cazul scrierii codului asta chiar ar strica.
    Se merge deci pe incredere in titlu, dar nu si pentru ferestrele astea.
#>
$Straine = @(
    'Chrome_WidgetWin_*', 'MozillaWindowClass', 'CabinetWClass',
    'ApplicationFrameWindow', 'Windows.UI.Core.*', 'Shell_TrayWnd',
    'Progman', 'WorkerW', 'ConsoleWindowClass', 'CASCADIA_HOSTING_WINDOW_CLASS'
)

function EStraina($clasa) {
    foreach ($tipar in $Straine) {
        if ($clasa -like $tipar) {
            return $true
        }
    }

    return $false
}

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
    $vazute = New-Object System.Collections.ArrayList

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

        [void]$vazute.Add([ordered]@{ titlu = $titlu; proces = $proces; clasa = $clasa })

        # Ori programul e unul cunoscut, ori pe fereastra scrie despre PIN. A
        # doua cale prinde furnizorii pe care nu-i știm încă pe nume.
        $alNostru = $Programe -contains $proces
        $spuneDePin = ($titlu -match $Vorbe) -and -not (EStraina $clasa)

        if ($alNostru -or $spuneDePin) {
            [void]$gasite.Add([ordered]@{
                titlu  = $titlu
                proces = $proces
                clasa  = $clasa
                # Ferestrele programelor cunoscute nu lasa loc de indoiala.
                sigura = $alNostru
            })
        }

        return $true
    }

    [void][DianaSoft.Ferestre]::EnumWindows($culegatorul, [IntPtr]::Zero)

    if ($Toate) {
        Raspunde ([ordered]@{
            deschisa = ($gasite.Count -gt 0)
            gasite   = @($gasite)
            vazute   = @($vazute)
        })
    }

    if ($gasite.Count -eq 0) {
        Raspunde ([ordered]@{
            deschisa = $false
            titlu    = ''
            proces   = ''
        })
    }

    # Intai cele ale programelor cunoscute, apoi cele gasite dupa titlu.
    $cea = @($gasite | Sort-Object -Property @{ Expression = { -not $_.sigura } })[0]

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
