# PowerShell script to start multiple queue workers for concurrent processing
Write-Host "Starting multiple queue workers for concurrent processing..." -ForegroundColor Green

# Function to start a queue worker
function Start-QueueWorker {
    param(
        [int]$WorkerNumber
    )
    
    $title = "Queue Worker $WorkerNumber"
    Write-Host "Starting $title..." -ForegroundColor Yellow
    
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "php artisan queue:work database --queue=default --sleep=1 --tries=3 --max-time=3600 --memory=512" -WindowStyle Normal
}

# Start 4 queue workers
for ($i = 1; $i -le 4; $i++) {
    Start-QueueWorker -WorkerNumber $i
    Start-Sleep -Seconds 2  # Small delay between starting workers
}

Write-Host "\n4 Queue workers started successfully!" -ForegroundColor Green
Write-Host "Each worker can process FindDriverForOrderJob concurrently." -ForegroundColor Cyan
Write-Host "\nTo stop all workers, close their respective PowerShell windows." -ForegroundColor Yellow
Write-Host "\nPress any key to exit this script..." -ForegroundColor White
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")