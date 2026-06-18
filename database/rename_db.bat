@echo off
echo Creating database itwekala_slaf_clms...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS itwekala_slaf_clms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if %ERRORLEVEL% neq 0 (
    echo Failed to create database itwekala_slaf_clms!
    exit /b 1
)

echo Restoring backup to itwekala_slaf_clms...
"C:\xampp\mysql\bin\mysql.exe" -u root itwekala_slaf_clms < "C:\xampp\htdocs\Computer-Laboratory-Management-System\database\slaf_clms_backup.sql"
if %ERRORLEVEL% equ 0 (
    echo Database rename completed successfully.
) else (
    echo Failed to restore backup!
)
