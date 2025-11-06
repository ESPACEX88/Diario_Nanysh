# Script para descargar e instalar extensiones PostgreSQL en Herd Lite
# Ejecuta este script como Administrador

$extDir = "C:\Users\posad\.config\herd-lite\bin\ext"
$phpVersion = "8.3"
$arch = "x64"
$vs = "vs16"
$nts = "nts"

Write-Host "Instalando extensiones PostgreSQL para PHP $phpVersion..." -ForegroundColor Green

# Crear directorio si no existe
New-Item -ItemType Directory -Force -Path $extDir | Out-Null

# URLs de las extensiones (pueden necesitar actualización)
# Intenta descargar desde diferentes fuentes

Write-Host "`nIntentando descargar php_pdo_pgsql.dll..." -ForegroundColor Yellow

# Opción 1: Desde PECL releases
$pdoUrl = "https://windows.php.net/downloads/pecl/releases/pdo_pgsql/1.0.5/php_pdo_pgsql-1.0.5-$nts-$vs-$arch.zip"
$pgsqlUrl = "https://windows.php.net/downloads/pecl/releases/pgsql/8.3.0/php_pgsql-8.3.0-$nts-$vs-$arch.zip"

try {
    Invoke-WebRequest -Uri $pdoUrl -OutFile "$env:TEMP\pdo_pgsql.zip" -ErrorAction Stop
    Write-Host "✓ Descargado php_pdo_pgsql.zip" -ForegroundColor Green
    
    Expand-Archive -Path "$env:TEMP\pdo_pgsql.zip" -DestinationPath "$env:TEMP\pdo_pgsql" -Force
    $dll = Get-ChildItem -Path "$env:TEMP\pdo_pgsql" -Filter "*.dll" -Recurse | Select-Object -First 1
    if ($dll) {
        Copy-Item $dll.FullName "$extDir\php_pdo_pgsql.dll" -Force
        Write-Host "✓ php_pdo_pgsql.dll instalado" -ForegroundColor Green
    }
} catch {
    Write-Host "✗ Error descargando pdo_pgsql: $_" -ForegroundColor Red
    Write-Host "`nPor favor, descarga manualmente desde:" -ForegroundColor Yellow
    Write-Host "https://pecl.php.net/package/pdo_pgsql" -ForegroundColor Cyan
}

Write-Host "`nIntentando descargar php_pgsql.dll..." -ForegroundColor Yellow

try {
    Invoke-WebRequest -Uri $pgsqlUrl -OutFile "$env:TEMP\pgsql.zip" -ErrorAction Stop
    Write-Host "✓ Descargado php_pgsql.zip" -ForegroundColor Green
    
    Expand-Archive -Path "$env:TEMP\pgsql.zip" -DestinationPath "$env:TEMP\pgsql" -Force
    $dll = Get-ChildItem -Path "$env:TEMP\pgsql" -Filter "*.dll" -Recurse | Select-Object -First 1
    if ($dll) {
        Copy-Item $dll.FullName "$extDir\php_pgsql.dll" -Force
        Write-Host "✓ php_pgsql.dll instalado" -ForegroundColor Green
    }
} catch {
    Write-Host "✗ Error descargando pgsql: $_" -ForegroundColor Red
    Write-Host "`nPor favor, descarga manualmente desde:" -ForegroundColor Yellow
    Write-Host "https://pecl.php.net/package/pgsql" -ForegroundColor Cyan
}

Write-Host "`nVerificando instalación..." -ForegroundColor Yellow
$pdoExists = Test-Path "$extDir\php_pdo_pgsql.dll"
$pgsqlExists = Test-Path "$extDir\php_pgsql.dll"

if ($pdoExists -and $pgsqlExists) {
    Write-Host "`n✓ Extensiones instaladas correctamente!" -ForegroundColor Green
    Write-Host "`nReinicia tu terminal y ejecuta:" -ForegroundColor Yellow
    Write-Host "php -m | Select-String 'pgsql'" -ForegroundColor Cyan
} else {
    Write-Host "`n✗ Algunas extensiones no se instalaron correctamente." -ForegroundColor Red
    Write-Host "`nInstalación manual requerida:" -ForegroundColor Yellow
    Write-Host "1. Ve a: https://pecl.php.net/package/pdo_pgsql" -ForegroundColor Cyan
    Write-Host "2. Descarga la versión para PHP 8.3 NTS x64" -ForegroundColor Cyan
    Write-Host "3. Extrae php_pdo_pgsql.dll a: $extDir" -ForegroundColor Cyan
    Write-Host "4. Repite para pgsql: https://pecl.php.net/package/pgsql" -ForegroundColor Cyan
}

