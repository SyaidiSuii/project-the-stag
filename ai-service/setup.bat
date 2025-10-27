@echo off
REM Setup script for AI Recommendation Service on Windows

echo ========================================
echo AI Recommendation Service - Setup
echo ========================================
echo.

REM Check Python installation
python --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Python is not installed or not in PATH
    echo Please install Python 3.8 or higher from https://www.python.org/
    pause
    exit /b 1
)

echo [INFO] Python found:
python --version
echo.

REM Create virtual environment
if exist "venv\" (
    echo [INFO] Virtual environment already exists
) else (
    echo [INFO] Creating virtual environment...
    python -m venv venv
    if errorlevel 1 (
        echo [ERROR] Failed to create virtual environment
        pause
        exit /b 1
    )
    echo [SUCCESS] Virtual environment created
)
echo.

REM Activate virtual environment
echo [INFO] Activating virtual environment...
call venv\Scripts\activate.bat
echo.

REM Upgrade pip
echo [INFO] Upgrading pip...
python -m pip install --upgrade pip
echo.

REM Install requirements
echo [INFO] Installing Python packages...
echo This may take a few minutes...
echo.
pip install -r requirements.txt
if errorlevel 1 (
    echo [ERROR] Failed to install requirements
    pause
    exit /b 1
)
echo.

REM Create .env if not exists
if exist ".env" (
    echo [INFO] .env file already exists
) else (
    echo [INFO] Creating .env file from example...
    copy .env.example .env
    echo [IMPORTANT] Please edit .env with your database credentials
)
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Edit .env file with your database credentials
echo 2. Run: run.bat
echo.
echo Deactivating virtual environment...
deactivate

pause
