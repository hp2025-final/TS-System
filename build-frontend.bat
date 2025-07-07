@echo off
echo ========================================
echo TS-POS-V1 Frontend Build Script
echo ========================================
echo.

echo [1/3] Installing Node.js dependencies...
call npm install
if %errorlevel% neq 0 (
    echo ERROR: npm install failed!
    echo Make sure Node.js is installed from https://nodejs.org
    pause
    exit /b 1
)

echo.
echo [2/3] Building frontend assets...
call npm run build
if %errorlevel% neq 0 (
    echo ERROR: npm run build failed!
    echo Check the error messages above.
    pause
    exit /b 1
)

echo.
echo [3/3] Build complete! 
echo ========================================
echo SUCCESS: Frontend assets have been built
echo You can now access the POS system at:
echo http://localhost/TS-POS-V1/public
echo ========================================
echo.
echo Demo Login:
echo Email: admin@tspos.com
echo Password: password
echo.
pause
