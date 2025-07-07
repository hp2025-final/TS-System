@echo off
echo Installing Node.js dependencies...
call npm install

echo Starting development server...
echo This will start Vite dev server with hot reload
echo Press Ctrl+C to stop the server
call npm run dev
