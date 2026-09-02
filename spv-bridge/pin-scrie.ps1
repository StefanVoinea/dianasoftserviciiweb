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
# SE INCEARCA O SINGURA DATA. Un cod gresit de trei ori blocheaza tokenul, deci
# nu se reia nimic aici si nu se scrie de doua ori pe aceeasi cerere: mai bine
# spunem ca n-a mers si asteptam omul, decat sa numaram noi incercarile lui.
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

<#
    Ce scrie pe fereastra unei cereri de PIN — cuvant cu cuvant ca in
    pin-fereastra.ps1. Cele doua trebuie sa inteleaga acelasi lucru prin
    „fereastra de PIN": cat aici era mai larg decat acolo, scrisul cantarea
    ferestre pe care sonda nici nu le vedea.
#>
$Vorbe = 'PIN|Token Logon|Token Password|Parola token|Smart ?Card|Autentificare|Authentication|Log ?[Oo]n|Introduce[tț]i'

<#
    Ferestrele mari de aplicatie, care nu sunt niciodata cereri de PIN.

    Aici lipsa lor era primejdioasa, nu doar suparatoare: un terminal SSH sau un
    browser cu „Authentication" in titlu intra in socoteala, iar dintre ele se
    alegea una in care sa se scrie codul.
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

function Uneltele {
    if (-not ('DianaSoft.Scrie' -as [type])) {
        Add-Type -Namespace DianaSoft -Name Scrie -MemberDefinition @'
[DllImport("user32.dll")]
public static extern bool EnumWindows(EnumWindowsProc lpEnumFunc, IntPtr lParam);

[DllImport("user32.dll")]
public static extern bool EnumChildWindows(IntPtr hWndParent, EnumWindowsProc lpEnumFunc, IntPtr lParam);

public delegate bool EnumWindowsProc(IntPtr hWnd, IntPtr lParam);

[DllImport("user32.dll")]
public static extern bool IsWindow(IntPtr hWnd);

[DllImport("user32.dll")]
public static extern bool IsWindowVisible(IntPtr hWnd);

[DllImport("user32.dll")]
public static extern bool IsWindowEnabled(IntPtr hWnd);

[DllImport("user32.dll", CharSet = CharSet.Unicode)]
public static extern int GetWindowTextW(IntPtr hWnd, System.Text.StringBuilder lpString, int nMaxCount);

[DllImport("user32.dll", CharSet = CharSet.Unicode)]
public static extern int GetClassNameW(IntPtr hWnd, System.Text.StringBuilder lpClassName, int nMaxCount);

[DllImport("user32.dll")]
public static extern int GetWindowThreadProcessId(IntPtr hWnd, out int lpdwProcessId);

[DllImport("user32.dll")]
public static extern bool SetForegroundWindow(IntPtr hWnd);

[DllImport("user32.dll")]
public static extern IntPtr GetForegroundWindow();

[DllImport("user32.dll")]
public static extern int GetWindowLongW(IntPtr hWnd, int nIndex);

[DllImport("user32.dll", CharSet = CharSet.Unicode)]
public static extern IntPtr SendMessageW(IntPtr hWnd, uint Msg, IntPtr wParam, IntPtr lParam);

[DllImport("user32.dll", CharSet = CharSet.Unicode)]
public static extern bool PostMessageW(IntPtr hWnd, uint Msg, IntPtr wParam, IntPtr lParam);

[DllImport("user32.dll")]
public static extern IntPtr GetDlgItem(IntPtr hDlg, int nIDDlgItem);
'@
    }
}

function Ferestrele {
    Uneltele

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

        if ($alNostru -or ((EDialog $clasa) -and ($titlu -match $Vorbe) -and -not (EStraina $clasa))) {
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

<#
    Copiii unei ferestre, cu clasa si stilul lor.

    De aici se afla caseta in care se scrie codul si butonul care il primeste.
#>
function Copiii($parinte) {
    Uneltele

    $gasiti = New-Object System.Collections.ArrayList

    $culegatorul = [DianaSoft.Scrie+EnumWindowsProc] {
        param($copil, $nefolosit)

        $clasa = New-Object System.Text.StringBuilder 256
        [void][DianaSoft.Scrie]::GetClassNameW($copil, $clasa, 256)

        $titlu = New-Object System.Text.StringBuilder 256
        [void][DianaSoft.Scrie]::GetWindowTextW($copil, $titlu, 256)

        [void]$gasiti.Add([ordered]@{
            fereastra = $copil
            clasa     = $clasa.ToString()
            titlu     = $titlu.ToString().Trim()
            # GWL_STYLE
            stil      = [DianaSoft.Scrie]::GetWindowLongW($copil, -16)
            pornit    = [DianaSoft.Scrie]::IsWindowEnabled($copil)
        })

        return $true
    }

    [void][DianaSoft.Scrie]::EnumChildWindows($parinte, $culegatorul, [IntPtr]::Zero)

    return $gasiti
}

<#
    S-a inchis fereastra in care am scris? Se asteapta putin.

    Se intreaba de ea anume, nu de toate ferestrele de PIN. Programul local e
    pornit in mai multe instante, iar cand doua dintre ele cer cheia deodata se
    deschid doua ferestre — una pentru fiecare. Cat se astepta sa nu mai fie
    niciuna, codul primit cum trebuie era dat drept nereusit: a doua fereastra,
    a altei instante, tinea raspunsul in loc. Iar omul vedea „fereastra a ramas
    deschisa" desi tokenul isi tinea deja minte codul.
#>
function SeInchide($fereastra, $rabdareMs) {
    $pana = (Get-Date).AddMilliseconds($rabdareMs)

    while ((Get-Date) -lt $pana) {
        if (-not [DianaSoft.Scrie]::IsWindow($fereastra) -or -not [DianaSoft.Scrie]::IsWindowVisible($fereastra)) {
            return $true
        }

        Start-Sleep -Milliseconds 250
    }

    return $false
}

try {
    Uneltele

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

    # Fereastra in fata. Se cere chiar ei, nu procesului: AppActivate ridica
    # fereastra principala a programului, care poate fi cu totul alta.
    [void][DianaSoft.Scrie]::SetForegroundWindow($cea.fereastra)
    Start-Sleep -Milliseconds 300

    $copiii = Copiii $cea.fereastra

    <#
        Casetele de scris, oricum s-ar chema.

        Numele lor tine de unealta cu care a fost facut programul: „Edit" la
        dialogurile Win32, „WindowsForms10.EDIT.app…" la cele in WinForms,
        „TEdit" la cele in Delphi. Se cauta deci bucata „edit" din nume, nu un
        nume anume — altfel jumatate din ferestrele de token trec nevazute, si
        se cade pe calea cu taste tocmai unde nu era nevoie.
    #>
    $eCaseta = '(^|\.)edit(\.|$)|richedit'

    # ES_PASSWORD: caseta care ascunde ce se scrie in ea. Aceea e a codului.
    $caseta = @($copiii | Where-Object {
        $_.clasa -imatch $eCaseta -and ($_.stil -band 0x20) -ne 0
    })[0]

    if (-not $caseta) {
        # Fara stilul acela, orice caseta de scris care e pornita.
        $caseta = @($copiii | Where-Object { $_.clasa -imatch $eCaseta -and $_.pornit })[0]
    }

    <#
        Oprelistea care nu tine de titluri.

        Titlurile se pot nimeri: „Authentication" scrie si pe ferestre care n-au
        nicio treaba cu tokenul. Dar o cerere de PIN are intotdeauna o caseta
        care ascunde ce se scrie in ea — sau, daca n-are nici casete pe care sa
        le vedem, e a unui program de token cunoscut pe nume.

        Fara niciuna dintre cele doua, codul nu pleaca nicaieri. Mai bine spunem
        ca n-am stiut unde sa-l scriem decat sa-l scriem intr-un terminal.
    #>
    $ascunsa = $caseta -and (($caseta.stil -band 0x20) -ne 0)

    if (-not $ascunsa -and -not $cea.sigura) {
        $motivul = 'fereastra găsită nu are casetă de cod ascunsă și nu e a unui program de token cunoscut; codul nu a fost trimis nicăieri'

        Raspunde ([ordered]@{ scris = $false; motiv = $motivul })
    }

    $peUndeAMers = ''

    if ($caseta) {
        <#
            Scrisul de-a dreptul in caseta.

            Nu depinde de cine e in fata: tastele nu se arunca in ecran, ci se
            pun in cutia postala a casetei. Asa cade toata loteria focusului —
            pricina cea mai deasa pentru care codul pleca in gol.

            Se scrie caracter cu caracter, nu tot textul deodata (WM_SETTEXT):
            multe dialoguri isi aprind butonul OK abia cand aud caseta
            schimbandu-se, iar textul pus dintr-o data nu se aude.
        #>
        $peUndeAMers = 'casetă'

        # WM_SETTEXT cu text gol: ce-a ramas de la o incercare de dinainte n-are
        # ce cauta inaintea codului nostru.
        [void][DianaSoft.Scrie]::SendMessageW($caseta.fereastra, 0x000C, [IntPtr]::Zero, [System.Runtime.InteropServices.Marshal]::StringToHGlobalUni(''))

        # WM_SETFOCUS pe caseta, ca butonul implicit sa fie cel asteptat.
        [void][DianaSoft.Scrie]::SendMessageW($caseta.fereastra, 0x0007, [IntPtr]::Zero, [IntPtr]::Zero)

        foreach ($litera in $pin.ToCharArray()) {
            # WM_CHAR
            [void][DianaSoft.Scrie]::PostMessageW($caseta.fereastra, 0x0102, [IntPtr][int]$litera, [IntPtr]0)
        }

        Start-Sleep -Milliseconds 300

        <#
            Butonul care primeste codul. Se cauta dupa nume, iar daca nu se
            gaseste niciunul se apasa Enter in caseta — dialogurile Win32 duc
            Enter la butonul implicit.
        #>
        $butonul = @($copiii | Where-Object {
            $_.clasa -imatch '(^|\.)button(\.|$)' -and $_.pornit -and
            ($_.titlu -replace '&', '') -match '^(OK|Log ?On|Autentificare|Continu|Accept|Da)'
        })[0]

        if ($butonul) {
            # BM_CLICK
            [void][DianaSoft.Scrie]::SendMessageW($butonul.fereastra, 0x00F5, [IntPtr]::Zero, [IntPtr]::Zero)
        } else {
            # WM_KEYDOWN / WM_KEYUP cu VK_RETURN
            [void][DianaSoft.Scrie]::PostMessageW($caseta.fereastra, 0x0100, [IntPtr]0x0D, [IntPtr]0)
            [void][DianaSoft.Scrie]::PostMessageW($caseta.fereastra, 0x0101, [IntPtr]0x0D, [IntPtr]0)
        }
    } else {
        <#
            Fereastra n-are casete pe care sa le vedem — asa sunt cele scrise in
            WPF, unde controalele n-au fereastra a lor. Atunci nu ramane decat
            calea veche: fereastra in fata si tastele in ecran.

            Semnele cu inteles pentru SendKeys se pun in paranteze drepte, altfel
            un „+" din cod ar insemna Shift si codul ar pleca schimbat.
        #>
        $peUndeAMers = 'taste'

        $inFata = [DianaSoft.Scrie]::GetForegroundWindow()

        if ($inFata -ne $cea.fereastra) {
            <#
                Tastele s-ar duce la altcineva. Nu le trimitem: un cod scris in
                fereastra gresita e in cel mai bun caz pierdut, si in cel mai rau
                ajunge unde nu trebuie.
            #>
            Raspunde ([ordered]@{
                scris = $false
                motiv = 'fereastra tokenului nu a putut fi adusă în față; codul nu a fost trimis nicăieri'
            })
        }

        Add-Type -AssemblyName System.Windows.Forms

        $deTrimis = [System.Text.RegularExpressions.Regex]::Replace($pin, '[+^%~(){}\[\]]', '{$0}')

        [System.Windows.Forms.SendKeys]::SendWait($deTrimis)
        Start-Sleep -Milliseconds 200
        [System.Windows.Forms.SendKeys]::SendWait('{ENTER}')

        $deTrimis = $null
    }

    # Codul nu mai are ce cauta in memorie de aici incolo.
    $pin = $null
    [System.GC]::Collect()

    <#
        S-a inchis fereastra? Daca da, codul a fost primit.

        Se asteapta cateva secunde, nu o clipa: dupa ce primeste codul, programul
        tokenului deschide cheia — si abia apoi isi inchide fereastra.
    #>
    if (SeInchide $cea.fereastra 5000) {
        <#
            Cate au mai ramas. Ele sunt ale celorlalte instante si cer acelasi
            cod; de cele mai multe ori se inchid singure, fiindca tokenul il tine
            minte odata dat. Se spune numarul lor, ca omul sa nu se mire daca mai
            e intrebat o data.
        #>
        $ramase = (Ferestrele).Count

        $vorba = ''

        if ($ramase -gt 0) {
            $vorba = 'a mai rămas deschisă o fereastră de PIN (' + $ramase + '), a altei lucrări'
        }

        Raspunde ([ordered]@{ scris = $true; motiv = $vorba; altele = $ramase })
    }

    <#
        A ramas deschisa. Se spune, pe cat se poate, si de ce: daca in caseta a
        ramas ce am scris noi, atunci tastele au ajuns si codul a fost cantarit —
        deci e gresit, sau butonul n-a primit apasarea. Daca s-a golit, programul
        tokenului l-a citit si l-a refuzat.

        Deosebirea conteaza: cu un cod gresit nu se mai incearca, fiindca trei
        greseli blocheaza tokenul.
    #>
    $motivul = 'fereastra a rămas deschisă'

    if ($peUndeAMers -eq 'casetă' -and [DianaSoft.Scrie]::IsWindowVisible($caseta.fereastra)) {
        # WM_GETTEXTLENGTH
        $cate = [DianaSoft.Scrie]::SendMessageW($caseta.fereastra, 0x000E, [IntPtr]::Zero, [IntPtr]::Zero)

        if ([int]$cate -gt 0) {
            $motivul = 'codul a ajuns în casetă, dar fereastra nu s-a închis — cel mai des codul e greșit'
        } else {
            $motivul = 'programul tokenului a citit codul și l-a refuzat — codul pare greșit'
        }
    } elseif ($peUndeAMers -eq 'taste') {
        $motivul = 'fereastra a rămas deschisă — codul poate fi greșit, sau programul tokenului nu primește taste din afară'
    }

    Raspunde ([ordered]@{ scris = $false; motiv = $motivul })
} catch {
    <#
        Nici aici nu se spune codul: numai ce a patit Windows.
    #>
    Raspunde ([ordered]@{ scris = $false; motiv = $_.Exception.Message })
}
