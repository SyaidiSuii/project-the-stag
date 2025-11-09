@echo off
title The Stag - Queue Worker
color 0A
echo ========================================
echo    THE STAG QUEUE WORKER
echo ========================================
echo.
echo Starting queue worker...
echo Keep this window OPEN while developing
echo.
echo Press Ctrl+C to stop
echo ========================================
echo.

cd /d D:\ProgramsFiles\laragon\www\the_stag
php artisan queue:work database --sleep=3 --tries=3 --timeout=90

pause
