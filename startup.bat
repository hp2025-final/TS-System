@echo off
title TS-POS-V1 System Startup

echo ========================================
echo TS-POS-V1 System Startup Script
echo ========================================

echo [1/6] Checking environment setup...
if not exist .env (
    echo Creating .env file from template...
    copy .env.example .env
    echo Please configure your .env file and run this script again.
    pause
    exit /b 1
)

echo [2/6] Installing/Updating dependencies...
echo Installing Node.js dependencies...
call npm install --silent

echo [3/6] Building frontend assets...
call npm run build

echo [4/6] Checking database setup...
php artisan migrate:status > nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo Running database migrations...
    php artisan migrate --force
    echo Seeding database with sample data...
    php artisan db:seed --force
)

echo [5/6] Generating application key if needed...
php artisan key:generate --show > nul 2>&1

echo [6/6] Starting servers...
echo.
echo ========================================
echo SERVERS STARTING
echo ========================================
echo Laravel Backend will be available at: http://localhost:8000
echo Vite Frontend will be available at: http://localhost:5173
echo.
echo Demo Login Credentials:
echo Email: admin@tspos.com
echo Password: password
echo ========================================
echo.

echo Starting Laravel server...
start "Laravel Server" php artisan serve --port=8000

timeout /t 3 > nul

echo Starting Vite development server...
start "Vite Dev Server" npm run dev

echo.
echo ========================================
echo SUCCESS: Both servers are starting!
echo ========================================
echo.
echo You can now access your TS-POS system at:
echo http://localhost:8000
echo.
echo Press any key to open the application in your browser...
pause > nul

start http://localhost:8000

echo.
echo ========================================
echo Setup complete! Both servers are running.
echo Close this window when you're done.
echo ========================================
