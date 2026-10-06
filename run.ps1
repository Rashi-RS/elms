Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "  Starting ELMS (Employee & Leave Management System)" -ForegroundColor Green
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "Server URL: http://localhost:8000" -ForegroundColor Yellow
Write-Host "Login URL:  http://localhost:8000/login" -ForegroundColor Yellow
Write-Host ""
Write-Host "Admin Login:    admin@elms.test / Admin@123" -ForegroundColor White
Write-Host "Employee Login: alex.turner@elms.test / Employee@123" -ForegroundColor White
Write-Host ""
Write-Host "Press Ctrl+C to stop the server.`n" -ForegroundColor Gray

& "C:\Users\Rashi Singh\.gemini\antigravity-ide\scratch\php\php.exe" -S localhost:8000 -t public
