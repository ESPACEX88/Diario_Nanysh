# Script para instalar extensiones PostgreSQL en PHP (Laragon)
Write-Host "=== Instalando Extensiones PostgreSQL para PHP ===" -ForegroundColor Cyan
Write-Host ""

# Rutas comunes de Laragon
$larationPaths = @(
    "C:\laragon\bin\php\php-8.3.11-nts-Win32-vs16-x64",
    "C:\laragon\bin\php\php-8.3-nts-Win32-vs16-x64",
    "C:\laragon\bin\php\php-8.3",
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
    Write-Host "No se encontro PHP" -ForegroundColor Red
    Write-Host "Busca manualmente la carpeta en C:\laragon\bin\php\" -ForegroundColor Yellow
    $phpPath = Read-Host "Ingresa la ruta completa de PHP"
    
    if (-not (Test-Path "$phpPath\php.exe")) {
        Write-Host "Ruta invalida" -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit
    }
}

Write-Host "PHP encontrado en: $phpPath" -ForegroundColor Green
Write-Host ""

# Verificar php.ini
$phpIni = "$phpPath\php.ini"
if (-not (Test-Path $phpIni)) {
    Write-Host "No se encontro php.ini, copiando php.ini-development..." -ForegroundColor Yellow
    Copy-Item "$phpPath\php.ini-development" $phpIni
}

Write-Host "Habilitando extensiones PostgreSQL..." -ForegroundColor Yellow
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
        $iniContent += "`n$ext"
        $changed = $true
    }
    else {
        Write-Host "OK - $ext ya esta habilitado" -ForegroundColor Green
    }
}

if ($changed) {
    Set-Content $phpIni $iniContent -NoNewline
    Write-Host ""
    Write-Host "Archivo php.ini actualizado" -ForegroundColor Green
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
        Write-Host "OK - $dll encontrado" -ForegroundColor Green
    } else {
        Write-Host "FALTA - $dll NO encontrado" -ForegroundColor Red
        $missingDlls += $dll
    }
}

if ($missingDlls.Count -gt 0) {
    Write-Host ""
    Write-Host "Faltan archivos DLL de PostgreSQL" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "SOLUCION:" -ForegroundColor Cyan
    Write-Host "1. Abre Laragon" -ForegroundColor White
    Write-Host "2. Menu -> PHP -> Quick add -> php_pdo_pgsql" -ForegroundColor White
    Write-Host "3. Reinicia Laragon" -ForegroundColor White
    Write-Host ""
    Write-Host "O descarga manualmente:" -ForegroundColor Cyan
    Write-Host "https://windows.php.net/downloads/pecl/releases/" -ForegroundColor White
}

Write-Host ""
Write-Host "=== Proximos pasos ===" -ForegroundColor Cyan
Write-Host "1. REINICIA LARAGON (Menu -> Stop -> Start)" -ForegroundColor White
Write-Host "2. Ejecuta: php artisan migrate" -ForegroundColor White
Write-Host ""

Read-Host "Presiona Enter para salir"
