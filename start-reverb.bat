@echo off
title Laravel Reverb Server
color 0A

echo ========================================
echo  LARAVEL REVERB WEBSOCKET SERVER
echo ========================================
echo.
echo Starting Reverb server on port 8080...
echo Keep this window OPEN!
echo Press Ctrl+C to stop the server
echo ========================================
echo.

cd /d D:\ProgramsFiles\laragon\www\the_stag
php artisan reverb:start

pause
