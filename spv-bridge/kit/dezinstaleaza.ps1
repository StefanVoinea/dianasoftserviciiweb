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

# Procesele PHP rămase pe portul programului
Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" | Where-Object {
    $_.CommandLine -like "*-S*:$Port*"
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
