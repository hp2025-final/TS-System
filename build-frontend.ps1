# TS-POS-V1 Frontend Build Script for PowerShell
Write-Host "========================================"
Write-Host "TS-POS-V1 Frontend Build Script"
Write-Host "========================================"
Write-Host ""

Write-Host "[1/3] Installing Node.js dependencies..." -ForegroundColor Cyan
try {
    npm install
    if ($LASTEXITCODE -ne 0) {
        throw "npm install failed"
    }
    Write-Host "✅ Dependencies installed successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ ERROR: npm install failed!" -ForegroundColor Red
    Write-Host "Make sure Node.js is installed from https://nodejs.org" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""
Write-Host "[2/3] Building frontend assets..." -ForegroundColor Cyan
try {
    npm run build
    if ($LASTEXITCODE -ne 0) {
        throw "npm run build failed"
    }
    Write-Host "✅ Frontend assets built successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ ERROR: npm run build failed!" -ForegroundColor Red
    Write-Host "Check the error messages above." -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host ""
Write-Host "[3/3] Build complete!" -ForegroundColor Green
Write-Host "========================================"
Write-Host "SUCCESS: Frontend assets have been built" -ForegroundColor Green
Write-Host "You can now access the POS system at:"
Write-Host "http://localhost/TS-POS-V1/public" -ForegroundColor Blue
Write-Host "========================================"
Write-Host ""
Write-Host "Demo Login:" -ForegroundColor Yellow
Write-Host "Email: admin@tspos.com"
Write-Host "Password: password"
Write-Host ""
Read-Host "Press Enter to exit"
