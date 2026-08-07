# Oprește și elimină programul care dă acces la tokenul ANAF.
param(
    [string]$NumeSarcina = 'Acces token ANAF',
    [int]$Port = 8099
)

$ErrorActionPreference = 'Continue'

$sarcina = Get-ScheduledTask -TaskName $NumeSarcina -ErrorAction SilentlyContinue

if ($sarcina) {
    Stop-ScheduledTask -TaskName $NumeSarcina -ErrorAction SilentlyContinue
    Unregister-ScheduledTask -TaskName $NumeSarcina -Confirm:$false
    Write-Host "Sarcina '$NumeSarcina' a fost eliminată." -ForegroundColor Green
} else {
    Write-Host "Nu există nicio sarcină cu numele '$NumeSarcina'." -ForegroundColor Yellow
}

# Instantele care lucrau in paralel, pe porturile vecine
foreach ($lucrator in @(Get-ScheduledTask -TaskName "$NumeSarcina - lucrator*" -ErrorAction SilentlyContinue)) {
    Stop-ScheduledTask -TaskName $lucrator.TaskName -ErrorAction SilentlyContinue
    Unregister-ScheduledTask -TaskName $lucrator.TaskName -Confirm:$false -ErrorAction SilentlyContinue
    Write-Host "Sarcina '$($lucrator.TaskName)' a fost eliminată." -ForegroundColor Green
}

# Agentul care aducea lucrul de la server, daca a fost instalat
$numeAgent = "$NumeSarcina - agent"
$sarcinaAgent = Get-ScheduledTask -TaskName $numeAgent -ErrorAction SilentlyContinue

if ($sarcinaAgent) {
    Stop-ScheduledTask -TaskName $numeAgent -ErrorAction SilentlyContinue
    Unregister-ScheduledTask -TaskName $numeAgent -Confirm:$false
    Write-Host "Sarcina '$numeAgent' a fost eliminată." -ForegroundColor Green
}

<#
    Procesele PHP rămase — programul de pe port, instanțele vecine și agentul.

    Se caută după dosarul acesta, nu după numele scripturilor: pe același
    calculator pot sta două instalări, pentru două firme, iar dezinstalarea
    uneia n-are ce căuta în cealaltă.
#>
$dosar = Split-Path -Parent $MyInvocation.MyCommand.Path

Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" -ErrorAction SilentlyContinue | Where-Object {
    $_.CommandLine -and ($_.CommandLine -like "*$dosar*" -or $_.CommandLine -like "*-S*:$Port*")
} | ForEach-Object {
    Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue
    Write-Host "Proces oprit: $($_.ProcessId)" -ForegroundColor Green
}

try {
    Remove-NetFirewallRule -DisplayName "Acces token $Port" -ErrorAction SilentlyContinue
    Write-Host "Regula de firewall a fost eliminată (dacă exista)." -ForegroundColor Green
} catch {
    Write-Host "Regula de firewall nu a putut fi eliminată (necesită administrator)." -ForegroundColor Yellow
}
