@echo off
rem  Instalarea programului de acces la certificatul digital.
rem
rem  Windows nu ruleaza fisiere .ps1 la dublu clic, iar cele venite dintr-o
rem  arhiva descarcata poarta "marca internetului", care opreste executia. De
rem  aceea exista acest fisier: se da dublu clic pe el si face totul singur.

setlocal
cd /d "%~dp0"

rem  Fereastra pe UTF-8: scripturile sunt scrise cu diacritice, iar in codul de
rem  pagini vechi mesajele ies pline de semne fara noima.
chcp 65001 >nul 2>&1

echo.
echo   Se instaleaza programul de acces la certificatul digital...
echo.

rem  Se deblocheaza tot dosarul, nu doar scripturile: si bibliotecile (.dll) si
rem  programele (.exe) sunt oprite de Windows cand vin dintr-o arhiva descarcata.
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Get-ChildItem -Path '%~dp0' -Recurse -File | Unblock-File" >nul 2>&1

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0instaleaza.ps1"

rem  Se spune ce s-a intamplat cu adevarat. Altfel un script care nici nu
rem  porneste se incheia tot cu "Gata", iar omul afla ca nu s-a instalat nimic
rem  abia peste zile, cand incearca sa lucreze.
if errorlevel 1 (
  echo.
  echo   INSTALAREA NU A REUSIT. Mesajele de mai sus spun de ce.
  echo   Trimiteti-le la asistenta, impreuna cu aceasta fereastra.
) else (
  echo.
  echo   Gata. Puteti inchide fereastra.
)

pause
