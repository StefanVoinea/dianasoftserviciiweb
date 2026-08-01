@echo off
rem  Oprirea si scoaterea programului de pe acest calculator.
rem
rem  Windows nu ruleaza fisiere .ps1 la dublu clic, iar pe cele venite dintr-o
rem  arhiva descarcata le opreste de la executie. De aceea exista acest fisier.
rem
rem  Se sterg cele doua sarcini programate (programul local si agentul) si se
rem  opresc procesele ramase. Dosarul, cu configurarea si arhiva de documente,
rem  ramane neatins — il stergeti dumneavoastra, daca doriti.

setlocal
cd /d "%~dp0"

echo.
echo   Se scoate programul de acces la certificatul digital...
echo.

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Get-ChildItem -Path '%~dp0*.ps1' | Unblock-File; & '%~dp0dezinstaleaza.ps1'"

echo.
echo   Gata. Documentele si configurarea au ramas in acest dosar.
pause
