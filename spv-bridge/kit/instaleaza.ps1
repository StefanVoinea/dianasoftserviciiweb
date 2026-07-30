# Instalează programul de acces la token ca sarcină programată care pornește automat la logon.
#
# Programul TREBUIE să ruleze sub contul utilizatorului care deține certificatul:
# certificatul de pe token stă în magazinul CurrentUser\My, invizibil pentru un
# serviciu Windows clasic (care rulează ca LocalSystem). Sarcina programată la
# logon are exact contextul potrivit și se comportă la fel: pornire automată,
# fără fereastră, repornire în caz de oprire.
param(
    [string]$Adresa = '127.0.0.1',
    [int]$Port = 8099,
    [string]$PhpPath = '',
    [string]$NumeSarcina = 'Acces token ANAF'
)

$ErrorActionPreference = 'Stop'
$folder = Split-Path -Parent $MyInvocation.MyCommand.Path

function Scrie($mesaj, $culoare = 'White') { Write-Host $mesaj -ForegroundColor $culoare }

Scrie "=== Instalare acces token ANAF ===" 'Cyan'

# 1. PHP — cel din kit are întâietate, ca să nu fie nevoie de instalări
$phpDinKit = Join-Path $folder 'php\php.exe'
$iniDinKit = Join-Path $folder 'php\php.ini'
$argumentePhp = ''

if (-not $PhpPath -and (Test-Path $phpDinKit)) {
    $PhpPath = $phpDinKit
    $argumentePhp = "-c `"$iniDinKit`" "
    Scrie "Se folosește PHP-ul din kit — nu trebuie instalat nimic." 'Green'
}

if (-not $PhpPath) {
    $comanda = Get-Command php.exe -ErrorAction SilentlyContinue
    if ($comanda) { $PhpPath = $comanda.Source }
}

if (-not $PhpPath -or -not (Test-Path $PhpPath)) {
    Scrie "PHP nu a fost găsit." 'Red'
    Scrie "Instalați PHP 7.3 sau mai nou (https://windows.php.net/download/) și reluați cu:" 'Yellow'
    Scrie "  .\instaleaza.ps1 -PhpPath 'C:\php\php.exe'" 'Yellow'
    exit 1
}

# Rulat, nu doar găsit: pe un Windows fără bibliotecile Visual C++, php.exe
# există dar nu pornește, iar sarcina programată ar eșua tăcut la fiecare logon.
try {
    $versiune = & $PhpPath -n -r "echo PHP_VERSION;" 2>&1
} catch {
    $versiune = ''
}

if (-not $versiune -or $versiune -notmatch '^\d+\.\d+') {
    Scrie "PHP a fost găsit la $PhpPath, dar nu pornește." 'Red'
    Scrie "Lipsesc, cel mai probabil, bibliotecile Visual C++ (vcredist x64):" 'Yellow'
    Scrie "  https://aka.ms/vs/17/release/vc_redist.x64.exe" 'Yellow'
    Scrie "Răspunsul primit: $versiune" 'DarkGray'
    exit 1
}

Scrie "PHP: $PhpPath (versiunea $versiune)"

# 2. Configurarea
$caleEnv = Join-Path $folder 'configurare.env'
if (-not (Test-Path $caleEnv)) {
    Scrie "Lipsește fișierul configurare.env de lângă acest script." 'Red'
    exit 1
}

# 3. Curăță o instalare anterioară
$existenta = Get-ScheduledTask -TaskName $NumeSarcina -ErrorAction SilentlyContinue
if ($existenta) {
    Scrie "Există deja o sarcină cu acest nume — se înlocuiește." 'Yellow'
    Unregister-ScheduledTask -TaskName $NumeSarcina -Confirm:$false
}

# 4. Sarcina programată
$argumente = "$argumentePhp-S $Adresa`:$Port server.php"
$actiune = New-ScheduledTaskAction -Execute $PhpPath -Argument $argumente -WorkingDirectory $folder
$declansator = New-ScheduledTaskTrigger -AtLogOn -User "$env:USERDOMAIN\$env:USERNAME"
$principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType Interactive -RunLevel Limited

$setari = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -RestartCount 999 `
    -RestartInterval (New-TimeSpan -Minutes 1) `
    -ExecutionTimeLimit (New-TimeSpan -Seconds 0) `
    -MultipleInstances IgnoreNew

Register-ScheduledTask -TaskName $NumeSarcina -Action $actiune -Trigger $declansator `
    -Principal $principal -Settings $setari -Description 'Dă aplicației acces la certificatul digital de pe tokenul conectat la acest calculator (SPV și depunere declarații ANAF).' | Out-Null

Scrie "Sarcina '$NumeSarcina' a fost creată (pornire la logon)." 'Green'

# 5. Regulă de firewall, dacă programul e expus în rețea
if ($Adresa -ne '127.0.0.1' -and $Adresa -ne 'localhost') {
    $numeRegula = "Acces token $Port"
    try {
        Remove-NetFirewallRule -DisplayName $numeRegula -ErrorAction SilentlyContinue
        New-NetFirewallRule -DisplayName $numeRegula -Direction Inbound -Action Allow `
            -Protocol TCP -LocalPort $Port -Profile Private, Domain | Out-Null
        Scrie "Regulă de firewall adăugată pentru portul $Port (rețele private și de domeniu)." 'Green'
    } catch {
        Scrie "Regula de firewall NU a putut fi adăugată (rulați ca administrator)." 'Yellow'
        Scrie "  Comandă manuală: New-NetFirewallRule -DisplayName '$numeRegula' -Direction Inbound -Action Allow -Protocol TCP -LocalPort $Port" 'Yellow'
    }
}

# 6. Pornire imediată
Start-ScheduledTask -TaskName $NumeSarcina
Start-Sleep -Seconds 3

try {
    $stare = (Get-ScheduledTask -TaskName $NumeSarcina).State
    Scrie "Stare sarcină: $stare"

    $test = & "$env:SystemRoot\System32\curl.exe" -sS -o NUL -w "%{http_code}" --max-time 10 "http://$Adresa`:$Port/certificate" 2>$null
    if ($test -eq '401') {
        Scrie "Programul răspunde pe http://$Adresa`:$Port (cere cod de acces — corect)." 'Green'
    } elseif ($test) {
        Scrie "Programul răspunde pe http://$Adresa`:$Port (cod $test)." 'Green'
    } else {
        Scrie "Programul nu a răspuns încă. Verificați cu: Get-ScheduledTask '$NumeSarcina'" 'Yellow'
    }
} catch {
    Scrie "Verificarea a eșuat: $($_.Exception.Message)" 'Yellow'
}

Scrie ""
Scrie "Gata. Programul va porni automat la fiecare autentificare a acestui utilizator." 'Cyan'
Scrie "Codul de acces se află în fișierul configurare.env și trebuie introdus în" 'Cyan'
Scrie "aplicație, la SPV -> Certificate digitale." 'Cyan'
