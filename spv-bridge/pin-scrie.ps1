# Scrie PIN-ul in fereastra care il asteapta si apasa OK.
#
# Codul vine pe intrarea standard, NU ca argument: argumentele se vad in lista
# de procese a calculatorului, si acolo n-are ce cauta un PIN. Nu se scrie
# nicaieri pe disc, nu se pune in niciun jurnal si nu se intoarce inapoi — nici
# macar in mesajul de eroare.
#
# Merge numai cand fereastra e deja deschisa: aici nu se forteaza nimic, se
# raspunde la o cerere pe care a facut-o tokenul singur.
#
# Nu la orice furnizor se poate. Unele programe de token isi apara dinadins
# fereastra de taste venite din afara — atunci se spune limpede ca n-a mers, si
# codul se scrie de mana, ca pana acum.

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


# Aceleasi semne dupa care se cunoaste fereastra ca in pin-fereastra.ps1.
$Programe = @(
    'SACSrv', 'SafeNetAuthenticationClient', 'eToken', 'eTSrv', 'eTBase',
    'iDProtect', 'bit4id', 'bit4xpki', 'AWP', 'certSIGN', 'DigiSign',
    'TokenAdmin', 'CryptoIdeMngr', 'ClassicClient', 'Gclib', 'SmartCardService',
    'dkck', 'CardOS', 'SecureStoreCSP'
)

$Vorbe = 'PIN|Token|Smart ?Card|Autentificare|Authentication|Log ?[Oo]n|Introduce|Enter'

function Ferestrele {
    if (-not ('DianaSoft.Scrie' -as [type])) {
        Add-Type -Namespace DianaSoft -Name Scrie -MemberDefinition @'
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

[DllImport("user32.dll")]
public static extern bool SetForegroundWindow(IntPtr hWnd);
'@
    }

    $gasite = New-Object System.Collections.ArrayList

    $culegatorul = [DianaSoft.Scrie+EnumWindowsProc] {
        param($fereastra, $nefolosit)

        if (-not [DianaSoft.Scrie]::IsWindowVisible($fereastra)) {
            return $true
        }

        $titlu = New-Object System.Text.StringBuilder 512
        [void][DianaSoft.Scrie]::GetWindowTextW($fereastra, $titlu, 512)
        $titlu = $titlu.ToString().Trim()

        if ($titlu -eq '') {
            return $true
        }

        $clasa = New-Object System.Text.StringBuilder 256
        [void][DianaSoft.Scrie]::GetClassNameW($fereastra, $clasa, 256)
        $clasa = $clasa.ToString()

        $idProces = 0
        [void][DianaSoft.Scrie]::GetWindowThreadProcessId($fereastra, [ref]$idProces)

        $proces = ''

        try {
            $proces = (Get-Process -Id $idProces -ErrorAction Stop).ProcessName
        } catch {
            $proces = ''
        }

        $alNostru = $Programe -contains $proces

        if ($alNostru -or ((EDialog $clasa) -and $titlu -match $Vorbe)) {
            [void]$gasite.Add([ordered]@{
                fereastra = $fereastra
                titlu     = $titlu
                proces    = $proces
                idProces  = $idProces
                # Ferestrele programelor cunoscute trec inaintea celor gasite
                # numai dupa titlu: acolo nu incape nicio indoiala.
                sigura    = $alNostru
            })
        }

        return $true
    }

    [void][DianaSoft.Scrie]::EnumWindows($culegatorul, [IntPtr]::Zero)

    return $gasite
}

try {
    # Codul vine pe intrarea standard, si numai de acolo.
    $pin = [Console]::In.ReadLine()

    if ([string]::IsNullOrEmpty($pin)) {
        Raspunde ([ordered]@{ scris = $false; motiv = 'nu a venit niciun cod' })
    }

    $gasite = Ferestrele

    if ($gasite.Count -eq 0) {
        Raspunde ([ordered]@{ scris = $false; motiv = 'nu e nicio fereastră de PIN deschisă acum' })
    }

    # Intai cele ale programelor cunoscute: codul n-are ce cauta intr-o
    # fereastra nimerita numai dupa titlu, daca e una sigura alaturi.
    $cea = @($gasite | Sort-Object -Property @{ Expression = { -not $_.sigura } })[0]

    # Fereastra trebuie sa fie in fata ca sa primeasca tastele.
    try {
        Add-Type -AssemblyName Microsoft.VisualBasic
        [Microsoft.VisualBasic.Interaction]::AppActivate($cea.idProces)
    } catch {
        [void][DianaSoft.Scrie]::SetForegroundWindow($cea.fereastra)
    }

    Start-Sleep -Milliseconds 400

    Add-Type -AssemblyName System.Windows.Forms

    <#
        Semnele cu inteles pentru SendKeys se pun in paranteze drepte, altfel
        un „+" din cod ar insemna Shift si codul ar pleca schimbat.
    #>
    $deTrimis = [System.Text.RegularExpressions.Regex]::Replace($pin, '[+^%~(){}\[\]]', '{$0}')

    [System.Windows.Forms.SendKeys]::SendWait($deTrimis)
    Start-Sleep -Milliseconds 200
    [System.Windows.Forms.SendKeys]::SendWait('{ENTER}')

    # Codul nu mai are ce cauta in memorie de aici incolo.
    $pin = $null
    $deTrimis = $null
    [System.GC]::Collect()

    # S-a inchis fereastra? Daca da, codul a fost primit.
    Start-Sleep -Milliseconds 1500

    $ramase = Ferestrele

    if ($ramase.Count -eq 0) {
        Raspunde ([ordered]@{ scris = $true; motiv = '' })
    }

    Raspunde ([ordered]@{
        scris = $false
        motiv = 'fereastra a rămas deschisă — codul poate fi greșit, sau programul tokenului nu primește taste din afară'
    })
} catch {
    <#
        Nici aici nu se spune codul: numai ce a pățit Windows.
    #>
    Raspunde ([ordered]@{ scris = $false; motiv = $_.Exception.Message })
}
