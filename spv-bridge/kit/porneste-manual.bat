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
  rem  Aceleasi optiuni, dar pe intelesul lansatorului: acolo fiecare argument
  rem  se da deoparte, intre ghilimelele lui.
  set "PHP_INI_VBS="-c" "%~dp0php\php.ini" "
) else (
  set "PHP_EXE=php"
  set "PHP_INI="
  set "PHP_INI_VBS="
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

rem  Instantele pleaca fara fereastra.
rem
rem  Ele nu au nimic de aratat: scriu doar randurile serverului de web, cate unul
rem  la fiecare cerere. Trei ferestre cu asa ceva incarcau bara de sarcini si se
rem  inchideau din greseala. Ce merita privit e jurnalul agentului, iar el ramane
rem  in fereastra lui (porneste-agent.bat).
rem
rem  Pornirea trece prin acelasi lansator ca sarcinile programate.
rem
rem  Nu doar ca sa fie un singur fel de a porni: procesele pornite din aceasta
rem  fereastra ii ramaneau legate, iar la inchiderea ei se opreau si ele — omul
rem  inchidea consola crezand ca a terminat, si odata cu ea se opreau
rem  descarcarile si dosarul urmarit. Wscript le desprinde cu adevarat: el iese
rem  indata, iar programul ramane singur, ca si cum l-ar fi pornit Windows.
rem
rem  Calea intreaga a lui server.php nu e de prisos: pe ea le recunoaste
rem  opreste-manual.bat. Fara ea, in lista proceselor Windows apare doar
rem  "php.exe -S ... server.php", la fel pentru orice dosar, si n-am
rem  avea cum sa oprim numai ce am pornit noi.
for %%p in (%PORTURI%) do (
  echo Pornesc programul pe portul %%p, fara fereastra.
  wscript //B //Nologo "%~dp0porneste-ascuns.vbs" "%PHP_EXE%" %PHP_INI_VBS%"-S" "127.0.0.1:%%p" "%~dp0server.php"
)

echo.
echo Acces token ANAF - pornit pe porturile: %PORTURI%
echo Programul lucreaza in fundal. Porniti acum si agentul: porneste-agent.bat
echo Pentru oprire: opreste-manual.bat
echo.
pause
