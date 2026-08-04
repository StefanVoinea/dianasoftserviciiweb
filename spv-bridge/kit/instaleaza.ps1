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

<#
    Fisierele venite dintr-o arhiva descarcata poarta marca internetului.
    Windows opreste scripturile din executie, iar .NET refuza sa incarce
    bibliotecile - de acolo vine eroarea 0x80131515 la semnare, cu itextsharp.
    Se deblocheaza tot dosarul, nu doar scripturile: si biblioteca de semnare,
    si programul de tiparit, si PHP-ul din kit au aceeasi patanie.
#>
Get-ChildItem -Path $folder -Recurse -File -ErrorAction SilentlyContinue | Unblock-File -ErrorAction SilentlyContinue
Scrie "Fisierele au fost deblocate (marca internetului)." 'Green'

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

<#
    3. Curăță o instalare anterioară.

    Dacă sarcina veche a fost făcută dintr-o fereastră de administrator, acum nu
    mai poate fi ștearsă: Windows răspunde "Access is denied". Se spune limpede
    ce e de făcut, în loc să se oprească totul cu un mesaj în engleză.
#>
$existenta = Get-ScheduledTask -TaskName $NumeSarcina -ErrorAction SilentlyContinue
if ($existenta) {
    Scrie "Există deja o sarcină cu acest nume — se înlocuiește." 'Yellow'

    try {
        Unregister-ScheduledTask -TaskName $NumeSarcina -Confirm:$false -ErrorAction Stop
    } catch {
        Scrie "Sarcina veche nu a putut fi ștearsă: $($_.Exception.Message)" 'Red'
        Scrie "A fost făcută, cel mai probabil, dintr-o fereastră de administrator." 'Yellow'
        Scrie "Dați clic dreapta pe instaleaza.bat și alegeți 'Run as administrator'." 'Yellow'
        exit 1
    }
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

# 4b. Agentul care aduce lucrul de la server
#
# Cu el, aplicatia nu mai trebuie sa poata suna la calculatorul acesta: agentul
# intreaba singur serverul ce are de facut, pe 443, ca orice pagina de internet.
# Asa nu se deschide niciun port pe router. Merge doar daca in configurare.env
# este scris PUNTE_SERVER; altfel nu se instaleaza nimic si legatura ramane
# directa, ca pana acum.
$continutEnv = Get-Content $caleEnv -Raw
$agentDePornit = $null

if ($continutEnv -match '(?m)^\s*PUNTE_SERVER\s*=\s*\S+') {
    $numeAgent = "$NumeSarcina - agent"

    $agentExistent = Get-ScheduledTask -TaskName $numeAgent -ErrorAction SilentlyContinue
    if ($agentExistent) {
        Unregister-ScheduledTask -TaskName $numeAgent -Confirm:$false
    }

    <#
        ${...}, nu `a: in ghilimele duble, `a este caracterul de control BEL
        (0x07), iar Task Scheduler refuza argumentul cu "Illegal xml character".
    #>
    $actiuneAgent = New-ScheduledTaskAction -Execute $PhpPath -Argument "${argumentePhp}agent.php" -WorkingDirectory $folder

    <#
        Se prinde esecul si se verifica dupa aceea ca sarcina chiar exista.

        Fara asta, o inregistrare picata trecea nespusa: instalarea pornea
        agentul o data, cu Start-Process, si totul parea in regula — pana la
        prima repornire a calculatorului, cand nu-l mai pornea nimeni, fiindca
        sarcina care trebuia sa-l porneasca la logon nu se facuse niciodata.
    #>
    try {
        Register-ScheduledTask -TaskName $numeAgent -Action $actiuneAgent -Trigger $declansator `
            -Principal $principal -Settings $setari `
            -Description 'Aduce de la aplicatie lucrul pentru tokenul de pe acest calculator, fara sa fie nevoie de porturi deschise.' -ErrorAction Stop | Out-Null
    } catch {
        Scrie "Sarcina '$numeAgent' NU a putut fi înregistrată: $($_.Exception.Message)" 'Red'
    }

    if (Get-ScheduledTask -TaskName $numeAgent -ErrorAction SilentlyContinue) {
        Scrie "Sarcina '$numeAgent' a fost creată (legătură prin tunel)." 'Green'

        # Pornirea lui vine după ce răspunde programul: agentul îi duce comenzile.
        $agentDePornit = $numeAgent
    } else {
        Scrie "Agentul nu va porni singur la pornirea calculatorului." 'Red'
        Scrie "Până la lămurirea cauzei, porniți-l cu porneste-agent.bat și lăsați fereastra deschisă." 'Yellow'
    }
} else {
    Scrie "Fără PUNTE_SERVER în configurare.env: legătura rămâne directă." 'Yellow'
}

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

<#
    7. Agentul, pornit acum, nu la următoarea autentificare.

    Până atunci aplicația n-ar avea pe unde ajunge la tokenul de aici, iar omul
    care tocmai a instalat ar încerca o operație și ar primi raspunsul ca
    programul de pe calculatorul cu tokenul nu ruleaza.
#>
if ($agentDePornit) {
    try {
        Start-ScheduledTask -TaskName $agentDePornit -ErrorAction Stop
    } catch {
        Scrie "Sarcina agentului nu a putut fi pornită: $($_.Exception.Message)" 'Yellow'
    }

    <#
        Se așteaptă să apară programul, nu se întreabă doar sarcina.

        Starea sarcinii e un răspuns ocolit: la unele așezări de Windows ea
        rămâne goală chiar și când programul rulează. Ce contează cu adevărat e
        dacă există un php care ține agentul, iar asta se vede în lista de
        procese.
    #>
    $agentPornit = $false

    foreach ($incercare in 1..10) {
        Start-Sleep -Seconds 1

        $procese = @(Get-CimInstance Win32_Process -Filter "Name='php.exe'" -ErrorAction SilentlyContinue |
            Where-Object { $_.CommandLine -like '*agent.php*' })

        if ($procese.Count -gt 0) {
            $agentPornit = $true
            break
        }
    }

    if ($agentPornit) {
        Scrie "Agentul a pornit și întreabă aplicația ce are de lucru." 'Green'
    } else {
        $stareAgent = (Get-ScheduledTask -TaskName $agentDePornit -ErrorAction SilentlyContinue).State

        if ($stareAgent) {
            Scrie "Agentul nu pare pornit (starea sarcinii: $stareAgent)." 'Yellow'
        } else {
            Scrie "Agentul nu pare pornit." 'Yellow'
        }

        # Agentul isi scrie pasii intr-un jurnal; ultimele randuri spun de ce s-a oprit.
        $caleJurnal = Join-Path $folder 'agent.log'

        # -Encoding UTF8: jurnalul e scris in UTF-8, iar fara asta randurile ies
        # pline de semne fara noima exact cand omul are nevoie sa le citeasca.
        if (Test-Path $caleJurnal) {
            Scrie "Ultimele rânduri din jurnalul agentului:" 'DarkGray'
            Get-Content $caleJurnal -Tail 5 -Encoding UTF8 | ForEach-Object { Scrie "  $_" 'DarkGray' }
        }

        Scrie "Porniți-l cu porneste-agent.bat și lăsați fereastra deschisă." 'Yellow'
    }
}

<#
    8. Ce va porni singur data viitoare.

    Se citesc sarcinile asa cum sunt ele acum in Windows, nu cum ar fi trebuit
    sa iasa instalarea: numai asta spune daca la urmatoarea pornire a
    calculatorului lucrurile chiar merg de la sine.
#>
Scrie ""
Scrie "La pornirea calculatorului vor porni singure:" 'Cyan'

foreach ($nume in @($NumeSarcina, "$NumeSarcina - agent")) {
    $sarcina = Get-ScheduledTask -TaskName $nume -ErrorAction SilentlyContinue

    if ($sarcina) {
        Scrie "  [da] $nume" 'Green'
    } elseif ($nume -eq $NumeSarcina -or $agentDePornit -or $continutEnv -match '(?m)^\s*PUNTE_SERVER\s*=\s*\S+') {
        Scrie "  [NU] $nume — va trebui pornit de fiecare data cu mana" 'Red'
    }
}

Scrie ""
Scrie "Gata. Programul va porni automat la fiecare autentificare a acestui utilizator." 'Cyan'
Scrie "Codul de acces se află în fișierul configurare.env și trebuie introdus în" 'Cyan'
Scrie "aplicație, la SPV -> Certificate digitale." 'Cyan'
