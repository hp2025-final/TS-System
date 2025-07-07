# TS-POS-V1 PowerShell Setup Commands

## Quick Setup Commands for PowerShell

### Option 1: Run each command separately
```powershell
npm install
npm run build
```

### Option 2: Run with error checking
```powershell
npm install; if ($LASTEXITCODE -eq 0) { npm run build }
```

### Option 3: Use the PowerShell script
```powershell
# Allow script execution (run as Administrator if needed)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Run the build script
.\build-frontend.ps1
```

### Option 4: Alternative command chaining
```powershell
npm install
if ($?) { npm run build }
```

## Development Mode
For development with hot reload:
```powershell
npm install
npm run dev
```

## Troubleshooting

### If you get execution policy errors:
```powershell
# Check current policy
Get-ExecutionPolicy

# Allow local scripts (recommended)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Or run with bypass (temporary)
PowerShell -ExecutionPolicy Bypass -File build-frontend.ps1
```

### If Node.js is not found:
1. Download and install Node.js from https://nodejs.org
2. Restart PowerShell
3. Verify with: `node --version`

### If npm is not found:
```powershell
# Node.js includes npm, but you can verify:
npm --version

# If missing, reinstall Node.js
```

## What Each Command Does

- `npm install` - Downloads and installs all frontend dependencies (Vue.js, Tailwind CSS, etc.)
- `npm run build` - Compiles Vue.js components and CSS into production-ready files
- `npm run dev` - Starts development server with hot reload for development

## After Successful Build

You'll see files created in:
- `public/build/manifest.json`
- `public/build/assets/`

Then access your POS system at: http://localhost/TS-POS-V1/public
