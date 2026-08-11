@echo off
rem  Opreste programul local pornit de mana din acest dosar.
rem
rem  Instantele lui lucreaza fara fereastra, deci nu se pot inchide cu mouse-ul.
rem  Aici se opresc numai cele pornite din dosarul acesta: pe alt calculator, sau
rem  in alt dosar, poate rula alt PHP care n-are nicio treaba cu noi.
rem
rem  Agentul are fereastra lui: pe el il opriti inchizand-o.
rem
rem  Nu atinge sarcinile programate. Pentru scoaterea lor cu totul, folositi
rem  dezinstaleaza.bat.
cd /d "%~dp0"

echo Opresc programul local pornit din acest dosar...

powershell -NoProfile -Command ^
  "$folder = (Get-Location).Path;" ^
  "$oprite = 0;" ^
  "Get-CimInstance Win32_Process -Filter \"Name='php.exe'\" | ForEach-Object {" ^
  "  $calea = $_.ExecutablePath; $linia = $_.CommandLine;" ^
  "  if (($calea -and $calea.StartsWith($folder)) -or ($linia -and $linia -like '*server.php*' -and $linia -like ('*' + $folder + '*'))) {" ^
  "    try { Stop-Process -Id $_.ProcessId -Force -ErrorAction Stop; $oprite++ } catch { }" ^
  "  }" ^
  "};" ^
  "if ($oprite -eq 0) { Write-Output 'Nu rula nimic pornit din acest dosar.' }" ^
  "else { Write-Output ('Oprite: ' + $oprite + ' instanta(e).') }"

echo.
pause
