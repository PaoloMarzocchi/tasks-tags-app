@echo off
REM Demo Authentication Flow Script for Windows
REM Make sure the Laravel server is running: php artisan serve

echo 🚀 Tasks & Tags API Authentication Demo
echo ========================================

REM Base URL
set BASE_URL=http://localhost:8000/api

echo.
echo 1️⃣ Health Check (Public Endpoint)
echo ----------------------------------
curl -s -X GET "%BASE_URL%/health"

echo.
echo.
echo 2️⃣ Login with Demo User
echo ------------------------
curl -s -X POST "%BASE_URL%/auth/login" -H "Content-Type: application/json" -d "{\"email\": \"demo@local\", \"password\": \"demo1234\"}"

echo.
echo.
echo 3️⃣ Get User Info (Protected Endpoint)
echo --------------------------------------
echo Note: You'll need to manually copy the token from the login response above
echo and use it in the Authorization header like this:
echo curl -X GET "%BASE_URL%/auth/me" -H "Authorization: Bearer YOUR_TOKEN_HERE"

echo.
echo.
echo 4️⃣ Try to Access Protected Endpoint Without Token
echo --------------------------------------------------
curl -s -X GET "%BASE_URL%/auth/me" -H "Content-Type: application/json"

echo.
echo.
echo ✅ Demo completed!
echo.
echo 📝 Notes:
echo - Server must be running: php artisan serve
echo - Demo user must exist: php artisan db:seed
echo - For full automation, use the bash script version on Linux/macOS
echo - Or use PowerShell for better JSON handling on Windows

pause
