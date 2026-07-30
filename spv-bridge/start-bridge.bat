@echo off
rem Pornește bridge-ul SPV pe 127.0.0.1:8099 (necesită token-ul USB conectat)
cd /d "%~dp0"
php -S 127.0.0.1:8099 server.php
