@echo off
title The Stag SmartDine - All Services Launcher
color 0B

echo ========================================
echo  THE STAG SMARTDINE - SERVICE LAUNCHER
echo ========================================
echo.
echo This will start 2 terminals:
echo  1. Laravel Development Server (port 8000)
echo  2. Reverb WebSocket Server (port 8080)
echo.
echo ========================================
echo.

:: Start Laravel Serve in new window
start "Laravel Server" cmd /k "cd /d D:\ProgramsFiles\laragon\www\the_stag && color 0E && echo ======================================== && echo  LARAVEL DEVELOPMENT SERVER && echo ======================================== && echo. && echo Server running on: http://localhost:8000 && echo Keep this window OPEN! && echo Press Ctrl+C to stop && echo ======================================== && echo. && php artisan serve"

:: Wait 2 seconds
timeout /t 2 /nobreak >nul

:: Start Reverb in new window
start "Reverb WebSocket" cmd /k "cd /d D:\ProgramsFiles\laragon\www\the_stag && color 0A && echo ======================================== && echo  REVERB WEBSOCKET SERVER && echo ======================================== && echo. && echo WebSocket server running on port 8080 && echo Keep this window OPEN! && echo Press Ctrl+C to stop && echo ======================================== && echo. && php artisan reverb:start"

echo.
echo ========================================
echo  ALL SERVICES STARTED!
echo ========================================
echo.
echo Laravel Server: http://localhost:8000
echo Reverb WebSocket: localhost:8080
echo.
echo Check the opened terminals for logs
echo Close this window when you're done
echo ========================================
echo.

pause
