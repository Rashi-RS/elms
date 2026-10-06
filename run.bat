@echo off
title ELMS - Employee & Leave Management System
echo ========================================================
echo   Starting ELMS (Employee & Leave Management System)
echo ========================================================
echo Server URL: http://localhost:8000
echo Login URL:  http://localhost:8000/login
echo.
echo Admin Login:    admin@elms.test / Admin@123
echo Employee Login: alex.turner@elms.test / Employee@123
echo.
echo Press Ctrl+C to stop the server.
echo ========================================================
"C:\Users\Rashi Singh\.gemini\antigravity-ide\scratch\php\php.exe" -S localhost:8000 -t public
pause
