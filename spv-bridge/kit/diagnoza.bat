@echo off
rem  Diagnoza programului de acces la certificatul digital.
rem
rem  Se da dublu clic pe acest fisier. Verifica, pe rand: programul local,
rem  legatura cu aplicatia, legatura cu ANAF, tokenul, semnarea si intrarea in
rem  SPV cu certificatul. La fiecare pas spune ce a iesit si, cand nu merge,
rem  care sunt pricinile obisnuite.

setlocal
cd /d "%~dp0"

rem  Fereastra pe UTF-8: numele de pe certificate au diacritice.
chcp 65001 >nul 2>&1

echo.
echo   Se verifica accesul la certificatul digital. Dureaza sub un minut.
echo.

rem  Fisierele venite dintr-o arhiva descarcata poarta "marca internetului",
rem  care opreste executia scripturilor.
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Get-ChildItem -Path '%~dp0' -Recurse -File | Unblock-File" >nul 2>&1

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0diagnoza.ps1"

echo.
echo   Raportul s-a scris si in fisierul diagnoza.txt, de langa acest program.
echo   Trimiteti-l la asistenta daca cereti ajutor.
echo.

pause
