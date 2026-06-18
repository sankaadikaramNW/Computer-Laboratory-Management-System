@echo off
echo Restoring original database slaf_clms...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS slaf_clms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if %ERRORLEVEL% neq 0 (
    echo Failed to recreate database slaf_clms!
    exit /b 1
)

"C:\xampp\mysql\bin\mysql.exe" -u root slaf_clms < "C:\xampp\htdocs\Computer-Laboratory-Management-System\database\slaf_clms_backup.sql"
if %ERRORLEVEL% equ 0 (
    echo Rollback completed successfully.
) else (
    echo Rollback failed!
)
