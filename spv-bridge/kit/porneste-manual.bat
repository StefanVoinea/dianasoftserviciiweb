@echo off
rem Pornire manuala a programului, cu fereastra vizibila (pentru verificari).
rem Pentru pornire automata la logon folositi instaleaza.bat.
cd /d "%~dp0"

rem  Fisierele venite dintr-o arhiva descarcata poarta "marca internetului":
rem  .NET refuza sa incarce itextsharp.dll si semnarea cade cu 0x80131515.
powershell -NoProfile -Command "Get-ChildItem -Path '%~dp0' -Recurse -File | Unblock-File" >nul 2>&1

rem PHP-ul din kit are intaietate: cu el nu trebuie instalat nimic pe calculator.
if exist "%~dp0php\php.exe" (
  set "PHP_EXE=%~dp0php\php.exe"
  set "PHP_INI=-c "%~dp0php\php.ini""
) else (
  set "PHP_EXE=php"
  set "PHP_INI="
)

rem  Instalarea porneste mai multe instante, pe porturi vecine, ca o descarcare
rem  lunga din SPV sa nu tina pe loc dosarul urmarit si celelalte lucrari. Aici
rem  se citesc de acolo: pornita de mana cu un singur port, verificarea ar arata
rem  altceva decat merge in fiecare zi.
set "PORTURI="
if exist "%~dp0configurare.env" (
  for /f "usebackq eol=# tokens=1,* delims==" %%a in ("%~dp0configurare.env") do (
    if /i "%%a"=="PUNTE_LOCAL_PORTURI" set "PORTURI=%%b"
  )
)
if not defined PORTURI set "PORTURI=8099"

rem  Celelalte instante pleaca fiecare in fereastra ei; prima ramane aici, ca
rem  inchiderea acestei ferestre sa opreasca ceva vazut, nu ceva ascuns.
set "PRIMUL="
for %%p in (%PORTURI%) do (
  if not defined PRIMUL (
    set "PRIMUL=%%p"
  ) else (
    echo Pornesc o instanta pe portul %%p, in fereastra ei.
    start "Acces token ANAF :%%p" "%PHP_EXE%" %PHP_INI% -S 127.0.0.1:%%p server.php
  )
)

echo.
echo Acces token ANAF - pornire manuala, pe porturile: %PORTURI%
echo Inchideti TOATE ferestrele deschise acum pentru a opri programul.
echo.
"%PHP_EXE%" %PHP_INI% -S 127.0.0.1:%PRIMUL% server.php
pause
