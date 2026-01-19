# Script para instalar extensiones PostgreSQL en PHP (Laragon)
Write-Host "=== Instalando Extensiones PostgreSQL para PHP ===" -ForegroundColor Cyan
Write-Host ""

# Detectar versión de PHP
$phpVersion = php -v 2>&1 | Select-String "PHP (\d+\.\d+)" | ForEach-Object { $_.Matches.Groups[1].Value }
Write-Host "PHP Version detectada: $phpVersion" -ForegroundColor Green
Write-Host ""

# Rutas comunes de Laragon
$larationPaths = @(
    "C:\laragon\bin\php\php-8.3.11-nts-Win32-vs16-x64",
    "C:\laragon\bin\php\php-8.3-nts-Win32-vs16-x64",
    "C:\php"
)

$phpPath = $null
foreach ($path in $larationPaths) {
    if (Test-Path "$path\php.exe") {
        $phpPath = $path
        break
    }
}

if (-not $phpPath) {
    Write-Host "❌ No se encontró la instalación de PHP" -ForegroundColor Red
    Write-Host "Busca manualmente la carpeta de PHP en C:\laragon\bin\php\" -ForegroundColor Yellow
    $phpPath = Read-Host "Ingresa la ruta completa de PHP"
    
    if (-not (Test-Path "$phpPath\php.exe")) {
        Write-Host "❌ Ruta inválida" -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit
    }
}

Write-Host "✅ PHP encontrado en: $phpPath" -ForegroundColor Green
Write-Host ""

# Verificar php.ini
$phpIni = "$phpPath\php.ini"
if (-not (Test-Path $phpIni)) {
    Write-Host "⚠️  No se encontró php.ini, copiando php.ini-development..." -ForegroundColor Yellow
    Copy-Item "$phpPath\php.ini-development" $phpIni
}

Write-Host "Verificando extensiones necesarias..." -ForegroundColor Yellow
Write-Host ""

# Leer php.ini
$iniContent = Get-Content $phpIni -Raw

# Extensiones a habilitar
$extensions = @(
    "extension=pdo_pgsql",
    "extension=pgsql"
)

$changed = $false

foreach ($ext in $extensions) {
    $escapedExt = [regex]::Escape($ext)
    if ($iniContent -match ";$escapedExt") {
        Write-Host "Habilitando $ext..." -ForegroundColor Yellow
        $iniContent = $iniContent -replace ";$escapedExt", $ext
        $changed = $true
    }
    elseif ($iniContent -notmatch $escapedExt) {
        Write-Host "Agregando $ext..." -ForegroundColor Yellow
        # Agregar al final del archivo
        $iniContent += "`n$ext"
        $changed = $true
    }
    else {
        Write-Host "OK: $ext ya esta habilitado" -ForegroundColor Green
    }
}

if ($changed) {
    Set-Content $phpIni $iniContent -NoNewline
    Write-Host ""
    Write-Host "✅ Archivo php.ini actualizado" -ForegroundColor Green
}

Write-Host ""
Write-Host "Verificando archivos DLL..." -ForegroundColor Yellow
Write-Host ""

# Verificar si existen las DLLs
$extPath = "$phpPath\ext"
$requiredDlls = @("php_pdo_pgsql.dll", "php_pgsql.dll")
$missingDlls = @()

foreach ($dll in $requiredDlls) {
    if (Test-Path "$extPath\$dll") {
        Write-Host "✅ $dll encontrado" -ForegroundColor Green
    } else {
        Write-Host "❌ $dll NO encontrado" -ForegroundColor Red
        $missingDlls += $dll
    }
}

if ($missingDlls.Count -gt 0) {
    Write-Host ""
    Write-Host "⚠️  Faltan archivos DLL de PostgreSQL" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "SOLUCIÓN:" -ForegroundColor Cyan
    Write-Host "1. Ve a: https://windows.php.net/downloads/pecl/releases/" -ForegroundColor White
    Write-Host "2. Busca 'pgsql' para PHP 8.3 NTS x64 VS16" -ForegroundColor White
    Write-Host "3. Descarga el archivo zip" -ForegroundColor White
    Write-Host "4. Extrae los archivos .dll a: $extPath" -ForegroundColor White
    Write-Host ""
    Write-Host "O ejecuta en Laragon:" -ForegroundColor Cyan
    Write-Host "Menu → PHP → Quick add → php_pdo_pgsql" -ForegroundColor White
}

Write-Host ""
Write-Host "=== Próximos pasos ===" -ForegroundColor Cyan
Write-Host "1. Reinicia Laragon (Menu → Stop/Start)" -ForegroundColor White
Write-Host "2. O reinicia el servicio de PHP" -ForegroundColor White
Write-Host "3. Ejecuta: php artisan migrate" -ForegroundColor White
Write-Host ""

Read-Host "Presiona Enter para salir"
