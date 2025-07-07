@echo off
echo ========================================
echo TS-POS-V1 System Status Check
echo ========================================

echo [1/4] Checking PHP version...
php -v

echo.
echo [2/4] Checking Laravel status...
php artisan --version

echo.
echo [3/4] Checking database migrations...
php artisan migrate:status

echo.
echo [4/4] Checking environment...
if exist .env (
    echo .env file exists
) else (
    echo WARNING: .env file not found
)

echo.
echo ========================================
echo Status check complete!
echo.
echo Your servers should be running at:
echo Laravel Backend: http://localhost:8000
echo Vite Frontend: http://localhost:5173
echo ========================================
pause
