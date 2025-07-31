@echo off
echo Starting multiple queue workers for concurrent processing...

REM Start 4 queue workers to handle multiple orders concurrently
start "Queue Worker 1" cmd /k "php artisan queue:work database --queue=default --sleep=1 --tries=3 --max-time=3600 --memory=512"
start "Queue Worker 2" cmd /k "php artisan queue:work database --queue=default --sleep=1 --tries=3 --max-time=3600 --memory=512"
start "Queue Worker 3" cmd /k "php artisan queue:work database --queue=default --sleep=1 --tries=3 --max-time=3600 --memory=512"
start "Queue Worker 4" cmd /k "php artisan queue:work database --queue=default --sleep=1 --tries=3 --max-time=3600 --memory=512"

echo Queue workers started! Each worker can process jobs concurrently.
echo Press any key to exit...
pause > nul