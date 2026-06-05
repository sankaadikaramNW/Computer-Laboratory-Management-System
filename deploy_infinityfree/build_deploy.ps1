$root = "f:\My projects\Computer Laboratory Management System"
$dest = "$root\deploy_infinityfree\htdocs"

Write-Host "Building deploy structure at: $dest" -ForegroundColor Cyan

# Clean and recreate htdocs
if (Test-Path $dest) { Remove-Item $dest -Recurse -Force }
New-Item -ItemType Directory -Path $dest | Out-Null

# Root files (deploy-specific versions)
Copy-Item "$root\deploy_infinityfree\index.php"  "$dest\index.php"
Copy-Item "$root\deploy_infinityfree\.htaccess"  "$dest\.htaccess"
Write-Host "Copied index.php and .htaccess" -ForegroundColor Green

# app/ folder
$appDest = "$dest\app"
New-Item -ItemType Directory -Path "$appDest\config" | Out-Null

Copy-Item "$root\app\controllers" "$appDest\controllers" -Recurse
Copy-Item "$root\app\core"        "$appDest\core"        -Recurse
Copy-Item "$root\app\helpers"     "$appDest\helpers"     -Recurse
Copy-Item "$root\app\models"      "$appDest\models"      -Recurse
Copy-Item "$root\app\views"       "$appDest\views"       -Recurse
Write-Host "Copied app/ (controllers, core, helpers, models, views)" -ForegroundColor Green

# Deploy config renamed to config.php (with real credentials)
Copy-Item "$root\deploy_infinityfree\config_for_infinityfree.php" "$appDest\config\config.php"
Write-Host "Copied config_for_infinityfree.php -> app/config/config.php" -ForegroundColor Green

# Static assets
Copy-Item "$root\css"    "$dest\css"    -Recurse
Copy-Item "$root\js"     "$dest\js"     -Recurse
Copy-Item "$root\images" "$dest\images" -Recurse
Write-Host "Copied css/, js/, images/" -ForegroundColor Green

# Show final structure
Write-Host ""
Write-Host "=== DEPLOY STRUCTURE (deploy_infinityfree\htdocs\) ===" -ForegroundColor Yellow
Get-ChildItem $dest -Recurse | ForEach-Object {
    $rel = $_.FullName.Replace($dest, "")
    Write-Host $rel
}
Write-Host ""
Write-Host "DONE - Upload contents of deploy_infinityfree\htdocs\ to InfinityFree htdocs\" -ForegroundColor Green
