@echo off
REM Batch script to run the AI Recommendation Service on Windows

echo ========================================
echo AI Recommendation Service - The Stag
echo ========================================
echo.

REM Check if virtual environment exists
if not exist "venv\" (
    echo [ERROR] Virtual environment not found!
    echo Please run setup.bat first
    pause
    exit /b 1
)

REM Activate virtual environment
echo [INFO] Activating virtual environment...
call venv\Scripts\activate.bat

REM Check if .env exists
if not exist ".env" (
    echo [WARNING] .env file not found!
    echo Copying .env.example to .env...
    copy .env.example .env
    echo.
    echo [IMPORTANT] Please edit .env with your database credentials
    echo Press any key when ready...
    pause
)

REM Run the Flask app
echo [INFO] Starting AI Recommendation Service...
echo.
python app.py

REM Deactivate on exit
deactivate
