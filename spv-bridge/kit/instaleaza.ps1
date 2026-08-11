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
    [string]$NumeSarcina = 'Acces token ANAF',
    <#
        Câte instanțe ale programului lucrează deodată.

        Serverul din PHP servește o singură cerere pe rând, așa că o descărcare
        lungă din SPV ținea pe loc și dosarul urmărit, și orice altă operație.
        Cu mai multe instanțe, pe porturi vecine, lucrările merg despărțite.
        Una singură (-Instante 1) înseamnă purtarea de dinainte.
    #>
    [int]$Instante = 3
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
# Aceeasi configurare, dar ca argument deoparte: lansatorul fara fereastra
# primeste fiecare bucata in parte, nu un sir intreg.
$PhpIni = ''

if (-not $PhpPath -and (Test-Path $phpDinKit)) {
    $PhpPath = $phpDinKit
    $argumentePhp = "-c `"$iniDinKit`" "
    $PhpIni = $iniDinKit
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

    Întâi se opresc sarcinile și procesele care rulează chiar acum.

    Ștergerea sarcinii nu oprește și programul pornit de ea: rămâne în picioare,
    iar instalarea îl mai pornește o dată. Așa ajungeau doi agenți să întrebe
    serverul și două programe să se bată pe același port — al doilea nici nu
    putea porni, dar sarcina îl tot încerca, la nesfârșit.

    Se opresc numai procesele din acest dosar: pe același calculator pot sta
    două instalări, pentru două firme, iar a doua n-are nicio vină.
#>
foreach ($veche in @(Get-ScheduledTask -TaskName "$NumeSarcina*" -ErrorAction SilentlyContinue)) {
    Stop-ScheduledTask -TaskName $veche.TaskName -ErrorAction SilentlyContinue
}

$dinDosar = @(Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" -ErrorAction SilentlyContinue |
    Where-Object { $_.CommandLine -and $_.CommandLine -like "*$folder*" })

foreach ($proces in $dinDosar) {
    Stop-Process -Id $proces.ProcessId -Force -ErrorAction SilentlyContinue
}

if ($dinDosar.Count -gt 0) {
    Scrie "S-au oprit $($dinDosar.Count) proces(e) rămase din instalarea de dinainte." 'Yellow'
    # Portul se eliberează cu o clipă întârziere; fără răgazul acesta, programul
    # nou pornește peste cel vechi și cade cu 'Address already in use'.
    Start-Sleep -Seconds 2
}

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

<#
  Actiunea sarcinii: programul pornit fara fereastra.

  Sarcina programata ruleaza php.exe de-a dreptul, iar Windows ii da consola —
  la fiecare intrare in cont se deschideau cate o fereastra de fiecare instanta.
  Ele n-au nimic de aratat, se inchideau din greseala, iar odata inchise se
  opreau si descarcarile, si dosarul urmarit.

  "Hidden" din Task Scheduler ascunde sarcina din lista, nu fereastra
  programului; singurul care poate porni ceva cu fereastra inchisa e wscript,
  prin lansatorul de langa noi.

  Calea intreaga a lui server.php nu e de prisos: dupa ea isi recunosc
  procesele opreste-manual.bat si dezinstaleaza.ps1.
#>
function ActiuneAscunsa([string[]]$argumenteProgram) {
    $lansator = Join-Path $folder 'porneste-ascuns.vbs'

    if (-not (Test-Path -LiteralPath $lansator)) {
        # Kit fara lansator: se merge ca inainte, cu fereastra.
        return New-ScheduledTaskAction -Execute $PhpPath `
            -Argument ($argumenteProgram -join ' ') -WorkingDirectory $folder
    }

    $bucati = @('//B', '//Nologo', ('"' + $lansator + '"'), ('"' + $PhpPath + '"'))

    foreach ($argument in $argumenteProgram) {
        $bucati += ('"' + $argument + '"')
    }

    return New-ScheduledTaskAction -Execute 'wscript.exe' `
        -Argument ($bucati -join ' ') -WorkingDirectory $folder
}

<# Argumentele programului local pentru un port anume, fiecare deoparte. #>
function ArgumenteleProgramului([string]$port) {
    $lista = @()

    if ($PhpIni -ne '') {
        $lista += '-c'
        $lista += $PhpIni
    }

    $lista += '-S'
    $lista += ($Adresa + ':' + $port)
    $lista += (Join-Path $folder 'server.php')

    return $lista
}

# 4. Sarcina programată
$actiune = ActiuneAscunsa (ArgumenteleProgramului $Port)
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

<#
    4a. Instanțele care lucrează deodată.

    Fiecare stă pe portul ei, vecin cu cel de bază, și e chemată de agent când
    cea dinainte e ocupată. Porturile se scriu în configurare.env, ca agentul să
    știe pe cine poate chema; fără rândul acela, el lucrează ca înainte, pe una
    singură.
#>
$porturi = @($Port)

for ($i = 1; $i -lt [Math]::Max(1, $Instante); $i++) {
    $portLucrator = $Port + $i
    $numeLucrator = "$NumeSarcina - lucrator $portLucrator"

    $existentLucrator = Get-ScheduledTask -TaskName $numeLucrator -ErrorAction SilentlyContinue
    if ($existentLucrator) {
        Unregister-ScheduledTask -TaskName $numeLucrator -Confirm:$false -ErrorAction SilentlyContinue
    }

    $actiuneLucrator = ActiuneAscunsa (ArgumenteleProgramului $portLucrator)

    try {
        Register-ScheduledTask -TaskName $numeLucrator -Action $actiuneLucrator -Trigger $declansator `
            -Principal $principal -Settings $setari `
            -Description 'Încă o instanță a programului de acces la token, ca lucrările să nu se aștepte una pe alta.' `
            -ErrorAction Stop | Out-Null

        Start-ScheduledTask -TaskName $numeLucrator -ErrorAction SilentlyContinue
        $porturi += $portLucrator
        Scrie "Instanță pe portul $portLucrator." 'Green'
    } catch {
        Scrie "Instanța de pe portul ${portLucrator} nu a putut fi creată: $($_.Exception.Message)" 'Yellow'
    }
}

<#
    Porturile ajung în configurare.env: agentul citește de acolo pe câte
    instanțe poate împărți lucrul. Rândul se înlocuiește dacă exista deja, ca o
    reinstalare cu alt număr de instanțe să nu lase în urmă porturi moarte.
#>
$randPorturi = 'PUNTE_LOCAL_PORTURI=' + ($porturi -join ',')
$continutVechi = Get-Content $caleEnv -Raw

if ($continutVechi -match '(?m)^\s*PUNTE_LOCAL_PORTURI\s*=.*$') {
    $continutNou = [regex]::Replace($continutVechi, '(?m)^\s*PUNTE_LOCAL_PORTURI\s*=.*$', $randPorturi)
} else {
    $continutNou = $continutVechi.TrimEnd() + "`r`n`r`n" +
        "# Instantele programului local care pot lucra deodata (scrise de instalare).`r`n" +
        $randPorturi + "`r`n"
}

Set-Content -Path $caleEnv -Value $continutNou -Encoding UTF8
Scrie "Lucrarile se impart pe porturile: $($porturi -join ', ')." 'Green'

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
    Se numără ce a rămas pornit, ca instalarea să spună singură dacă a ieșit
    strâmb: câte un program pe fiecare port, plus agentul — nici mai mult, nici
    mai puțin. Un număr în plus înseamnă că a rămas ceva de dinainte, iar doi
    agenți întreabă serverul de două ori pentru aceeași treabă.
#>
$pornite = @(Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" -ErrorAction SilentlyContinue |
    Where-Object { $_.CommandLine -and $_.CommandLine -like "*$folder*" })

$agenti = @($pornite | Where-Object { $_.CommandLine -like '*agent.php*' }).Count
$programe = @($pornite | Where-Object { $_.CommandLine -like '*server.php*' }).Count
$asteptate = $porturi.Count

$culoare = 'Green'
if ($programe -gt $asteptate -or $agenti -gt 1) { $culoare = 'Yellow' }

Scrie "Rulează $programe instanță(e) a programului și $agenti agent." $culoare

if ($programe -gt $asteptate -or $agenti -gt 1) {
    Scrie "Sunt mai multe decât trebuie ($asteptate instanțe și un agent)." 'Yellow'
    Scrie "Reporniți calculatorul, sau rulați dezinstaleaza.bat și apoi instaleaza.bat din nou." 'Yellow'
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
