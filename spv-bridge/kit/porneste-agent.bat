@echo off
rem  Pornire manuala a agentului, cu fereastra vizibila (pentru verificari).
rem
rem  Agentul e cel care intreaba aplicatia daca are ceva de lucru pentru tokenul
rem  de pe acest calculator. Lucreaza impreuna cu programul local: porniti-l pe
rem  acela intai (porneste-manual.bat), apoi pe acesta, in alta fereastra.
rem
rem  Pentru pornire automata la fiecare logon, folositi instaleaza.bat.
cd /d "%~dp0"

rem PHP-ul din kit are intaietate: cu el nu trebuie instalat nimic pe calculator.
if exist "%~dp0php\php.exe" (
  set "PHP_EXE=%~dp0php\php.exe"
  set "PHP_INI=-c "%~dp0php\php.ini""
) else (
  set "PHP_EXE=php"
  set "PHP_INI="
)

echo Agent - pornire manuala. Inchideti fereastra pentru a-l opri.
echo.
"%PHP_EXE%" %PHP_INI% agent.php
pause
