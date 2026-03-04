@echo off
echo Synchronizing project into Apache root...
xcopy "%~dp0" "C:\xampp\htdocs\CLASS-SCHEDULING-WITH-DATABASE-main\" /E /H /Y /I > nul 2>&1
echo Checking XAMPP Services...
echo.

REM Check if apache is running
tasklist /FI "IMAGENAME eq apache2.exe" 2>NUL | find /I /N "apache2.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Apache is running
) else (
    echo [STARTING] Apache...
    cd C:\xampp
    apache_start.bat
    timeout /t 3 /nobreak
)

REM Check if mysql is running
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL is running
) else (
    echo [STARTING] MySQL...
    cd C:\xampp
    mysql_start.bat
    timeout /t 3 /nobreak
)

echo.
echo [SUCCESS] Services started. Opening test page...
timeout /t 2 /nobreak

REM Open browser
start http://localhost/CLASS-SCHEDULING-WITH-DATABASE-main/ 

pause
