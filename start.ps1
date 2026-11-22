# Starts Lab 11 (Passport Control) at http://localhost:8115
# Loads pdo_sqlite explicitly, because the winget PHP build has no php.ini.
$php = (Get-Command php -ErrorAction Stop).Source
$ext = Join-Path (Split-Path $php) 'ext'
Write-Host "Lab 11 (Passport Control) running at http://localhost:8115  (Ctrl+C to stop)"
& $php -d "extension_dir=$ext" -d extension=pdo_sqlite -S localhost:8115 -t $PSScriptRoot
