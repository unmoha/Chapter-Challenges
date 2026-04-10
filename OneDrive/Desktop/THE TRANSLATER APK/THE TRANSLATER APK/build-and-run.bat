@echo off
setlocal enabledelayedexpansion

REM Set project directory
set PROJECT_DIR=%~dp0
set SRC_DIR=%PROJECT_DIR%src\main\java
set BIN_DIR=%PROJECT_DIR%target\classes
set LIB_DIR=%PROJECT_DIR%target\dependency

REM Create directories if they don't exist
if not exist "%BIN_DIR%" mkdir "%BIN_DIR%"
if not exist "%LIB_DIR%" mkdir "%LIB_DIR%"

REM Compile the project
echo Compiling project...
if not exist "%JAVA_HOME%\bin\javac.exe" (
    echo Error: JAVA_HOME is not set or does not point to a valid JDK
    exit /b 1
)

"%JAVA_HOME%\bin\javac" -d "%BIN_DIR%" -cp "%BIN_DIR%;%LIB_DIR%\*" -sourcepath "%SRC_DIR%" "%SRC_DIR%\com\translator\ui\TranslatorApp.java" "%SRC_DIR%\com\translator\server\TranslationServer.java"
if %ERRORLEVEL% neq 0 (
    echo Compilation failed
    exit /b 1
)

echo.
echo ============================================
echo 1. Starting RMI Registry...
start "RMI Registry" cmd /c "%JAVA_HOME%\bin\rmiregistry 1099"
timeout /t 2 >nul

echo 2. Starting Translation Server...
start "Translation Server" cmd /k "%JAVA_HOME%\bin\java" -Djava.security.policy=server.policy -cp "%BIN_DIR%;%LIB_DIR%\*" com.translator.server.TranslationServer

timeout /t 2 >nul

echo 3. Starting Translation Client...
start "Translation Client" "%JAVA_HOME%\bin\java" -Djava.security.policy=client.policy -cp "%BIN_DIR%;%LIB_DIR%\*" com.translator.ui.TranslatorApp

echo.
echo ============================================
echo Application started! You can now use the translator.
echo.

exit /b 0
