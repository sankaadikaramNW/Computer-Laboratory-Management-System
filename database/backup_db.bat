@echo off
echo Backing up database slaf_clms...
"C:\xampp\mysql\bin\mysqldump.exe" -u root slaf_clms > "C:\xampp\htdocs\Computer-Laboratory-Management-System\database\slaf_clms_backup.sql"
if %ERRORLEVEL% equ 0 (
    echo Backup successful: database/slaf_clms_backup.sql
) else (
    echo Backup failed!
)
