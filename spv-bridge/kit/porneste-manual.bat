@echo off
rem Pornire manuala a programului, cu fereastra vizibila (pentru verificari).
rem Pentru pornire automata la logon folositi instaleaza.ps1.
cd /d "%~dp0"

rem PHP-ul din kit are intaietate: cu el nu trebuie instalat nimic pe calculator.
if exist "%~dp0php\php.exe" (
  set "PHP_EXE=%~dp0php\php.exe"
  set "PHP_INI=-c "%~dp0php\php.ini""
) else (
  set "PHP_EXE=php"
  set "PHP_INI="
)

echo Acces token ANAF - pornire manuala. Inchideti fereastra pentru a-l opri.
"%PHP_EXE%" %PHP_INI% -S 127.0.0.1:8099 server.php
pause
