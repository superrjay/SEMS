@echo off
REM ====================================================================
REM MASTER STARTUP SCRIPT - Scheduling System
REM ====================================================================

echo.
echo ============================================================
echo      SCHEDULING SYSTEM - AUTO STARTUP
echo ============================================================
echo.

REM Check if XAMPP is installed
if not exist "C:\xampp\apache\bin\httpd.exe" (
    echo ERROR: XAMPP not found at C:\xampp
    echo Please install XAMPP first!
    pause
    exit /b 1
)

echo [1/4] Synchronizing project into Apache root...
REM copy changed files into htdocs so Apache sees the latest edits
xcopy "%~dp0" "C:\xampp\htdocs\CLASS-SCHEDULING-WITH-DATABASE-main\" /E /H /Y /I > nul 2>&1
echo [1/3] Starting Apache Web Server...
cd /d "C:\xampp"
call apache_start.bat > nul 2>&1
timeout /t 2 > nul

echo [2/3] Starting MySQL Database Server...
call mysql_start.bat > nul 2>&1
timeout /t 3 > nul

echo [3/3] Opening System in Browser...
start "" "http://localhost/CLASS-SCHEDULING-WITH-DATABASE-main/"

echo.
echo ============================================================
echo      ✅ SYSTEM STARTED SUCCESSFULLY!
echo ============================================================
echo.
echo NOTE: this script assumes the project folder has been copied to C:\xampp\htdocs\CLASS-SCHEDULING-WITH-DATABASE-main
echo If you see red "Database NOT connected" message:
echo    1. Wait 5 more seconds for MySQL to fully start
echo    2. Refresh the page (F5)
echo    3. Check XAMPP Control Panel - both Apache and MySQL must be GREEN
echo.
echo If still not working:
echo    1. Close XAMPP completely
echo    2. Open XAMPP Control Panel manually
echo    3. Click START for Apache and MySQL
echo    4. Wait for them to turn GREEN
echo    5. Refresh browser
echo.
pause
