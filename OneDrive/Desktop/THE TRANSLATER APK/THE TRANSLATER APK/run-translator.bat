@echo off
echo Starting Translator Desktop...
cd "ui-fx\target"
java -jar translator-desktop-0.1.0-jar-with-dependencies.jar
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo Error starting application!
    echo Make sure Java 17+ is installed.
    echo.
)
pause
