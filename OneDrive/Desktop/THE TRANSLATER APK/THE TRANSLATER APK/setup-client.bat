@echo off
echo ========================================
echo Setting up Translation Client
echo ========================================
echo.

REM Check if Java is installed
java -version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Java is not installed!
    echo Please install Java 17+ from https://java.com
    echo.
    pause
    exit /b 1
)

echo Java found:
java -version
echo.

REM Get server IP from user
set /p SERVER_IP="Enter server IP address (e.g., 192.168.1.100): "

if "%SERVER_IP%"=="" (
    echo ERROR: Server IP is required!
    pause
    exit /b 1
)

echo.
echo Testing connection to server...
ping -n 1 %SERVER_IP% >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Cannot ping server at %SERVER_IP%
    echo Please check:
    echo 1. Server IP is correct
    echo 2. Server computer is on
    echo 3. Network connection is working
    echo.
    set /p CONTINUE="Continue anyway? (y/n): "
    if /i not "%CONTINUE%"=="y" exit /b 1
)

REM Test translation service
echo Testing translation service...
curl -s http://%SERVER_IP%:5000/spec.json >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Cannot reach translation service at %SERVER_IP%:5000
    echo Please check:
    echo 1. LibreTranslate server is running on server computer
    echo 2. Port 5000 is open on server firewall
    echo 3. Server IP is correct
    echo.
    set /p CONTINUE="Continue anyway? (y/n): "
    if /i not "%CONTINUE%"=="y" exit /b 1
) else (
    echo SUCCESS: Translation service is reachable!
)

echo.
echo Creating client configuration...
echo translation.engine=libretranslate > client-config.properties
echo translation.url=http://%SERVER_IP%:5000/ >> client-config.properties
echo translation.timeout=30000 >> client-config.properties
echo ui.theme=light >> client-config.properties
echo ui.language=en >> client-config.properties
echo feature.voice=true >> client-config.properties
echo feature.ocr=true >> client-config.properties
echo feature.history=true >> client-config.properties

echo.
echo Creating client launcher...
echo @echo off > run-client.bat
echo echo Starting Translator Desktop Client... >> run-client.bat
echo echo Server: %SERVER_IP%:5000 >> run-client.bat
echo echo. >> run-client.bat
echo java -jar translator-desktop-0.1.0-jar-with-dependencies.jar >> run-client.bat
echo if %%ERRORLEVEL%% NEQ 0 ( >> run-client.bat
echo     echo. >> run-client.bat
echo     echo Failed to connect to server! >> run-client.bat
echo     echo Please check server connectivity. >> run-client.bat
echo ) >> run-client.bat
echo pause >> run-client.bat

echo.
echo ========================================
echo Client Setup Complete
echo ========================================
echo.
echo Configuration files created:
echo   - client-config.properties
echo   - run-client.bat
echo.
echo To start the application:
echo   1. Make sure translator-desktop-0.1.0-jar-with-dependencies.jar is in this folder
echo   2. Double-click run-client.bat
echo.
echo Server: %SERVER_IP%:5000
echo.

pause
