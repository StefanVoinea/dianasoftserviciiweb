@echo off
rem  Instalarea programului de acces la certificatul digital.
rem
rem  Windows nu ruleaza fisiere .ps1 la dublu clic, iar cele venite dintr-o
rem  arhiva descarcata poarta "marca internetului", care opreste executia. De
rem  aceea exista acest fisier: se da dublu clic pe el si face totul singur.

setlocal
cd /d "%~dp0"

echo.
echo   Se instaleaza programul de acces la certificatul digital...
echo.

rem  Se deblocheaza tot dosarul, nu doar scripturile: si bibliotecile (.dll) si
rem  programele (.exe) sunt oprite de Windows cand vin dintr-o arhiva descarcata.
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Get-ChildItem -Path '%~dp0' -Recurse -File | Unblock-File; & '%~dp0instaleaza.ps1'"

echo.
echo   Gata. Puteti inchide fereastra.
pause
