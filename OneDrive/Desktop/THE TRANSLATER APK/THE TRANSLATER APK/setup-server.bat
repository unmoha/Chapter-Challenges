@echo off
echo ========================================
echo Setting up LibreTranslate Server
echo ========================================
echo.

REM Check if Docker is installed
docker --version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Docker is not installed!
    echo Please install Docker Desktop from https://docker.com
    echo.
    pause
    exit /b 1
)

echo Docker found: 
docker --version
echo.

REM Stop existing container if running
echo Stopping existing LibreTranslate container...
docker stop libretranslate 2>nul
docker rm libretranslate 2>nul
echo.

REM Start new LibreTranslate container
echo Starting LibreTranslate server on port 5000...
docker run -d --name libretranslate -p 5000:5000 --restart unless-stopped libretranslate/libretranslate

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to start LibreTranslate container!
    pause
    exit /b 1
)

echo.
echo Server starting up... (waiting 10 seconds)
timeout /t 10 /nobreak >nul

REM Test server
echo Testing server...
curl -s http://localhost:5000/spec.json >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Server test failed, but container may still be starting
    echo Wait a few more minutes and try accessing: http://localhost:5000
) else (
    echo SUCCESS: Server is running!
)

echo.
echo ========================================
echo Server Setup Complete
echo ========================================
echo.
echo Server URL: http://localhost:5000
echo.
echo To find your IP address for clients:
ipconfig | findstr "IPv4"
echo.
echo Client computers should use: http://[YOUR_IP]:5000
echo.
echo To manage server:
echo   View logs: docker logs libretranslate
echo   Stop server: docker stop libretranslate
echo   Start server: docker start libretranslate
echo.

pause
