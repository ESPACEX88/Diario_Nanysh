# Script para importar backup de Render usando PostgreSQL 17
Write-Host "=== Importacion de Backup de Render a Supabase ===" -ForegroundColor Cyan
Write-Host ""

# Buscar PostgreSQL 17 primero, luego 16, luego 15
$pgRestorePath = $null
$pgVersions = @(
    "C:\Program Files\PostgreSQL\17\bin\pg_restore.exe",
    "C:\Program Files\PostgreSQL\16\bin\pg_restore.exe",
    "C:\Program Files\PostgreSQL\15\bin\pg_restore.exe"
)

foreach ($path in $pgVersions) {
    if (Test-Path $path) {
        $pgRestorePath = $path
        $version = ($path -split '\\')[3]
        Write-Host "Usando PostgreSQL $version" -ForegroundColor Green
        break
    }
}

if (-not $pgRestorePath) {
    Write-Host "PostgreSQL no encontrado" -ForegroundColor Red
    Write-Host ""
    Write-Host "Instalando PostgreSQL 17 (necesario para backup de Render)..." -ForegroundColor Yellow
    Write-Host ""
    
    try {
        winget install PostgreSQL.PostgreSQL.17
        Write-Host ""
        Write-Host "PostgreSQL 17 instalado" -ForegroundColor Green
        Write-Host "Ejecuta este script nuevamente" -ForegroundColor Yellow
        Read-Host "Presiona Enter para salir"
        exit
    } catch {
        Write-Host "Error al instalar PostgreSQL 17" -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit
    }
}

# Verificar que sea version 16 o superior para el backup 1.16
if ($pgRestorePath -like "*\15\*") {
    Write-Host "PostgreSQL 15 no puede leer backups version 1.16" -ForegroundColor Red
    Write-Host "Instalando PostgreSQL 17..." -ForegroundColor Yellow
    Write-Host ""
    
    winget install PostgreSQL.PostgreSQL.17
    
    if (Test-Path "C:\Program Files\PostgreSQL\17\bin\pg_restore.exe") {
        $pgRestorePath = "C:\Program Files\PostgreSQL\17\bin\pg_restore.exe"
        Write-Host "PostgreSQL 17 instalado correctamente" -ForegroundColor Green
    } else {
        Write-Host "Error: Cierra esta terminal y ejecuta el script nuevamente" -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit
    }
}

Write-Host ""

# Ruta del backup
$backupPath = "C:\Users\posad\Downloads\2026-01-19T16_30Z.dir\2026-01-19T16_30Z\diario_fhd4"

Write-Host "Verificando backup en: $backupPath" -ForegroundColor Yellow

if (-not (Test-Path $backupPath)) {
    Write-Host "No se encontro el backup" -ForegroundColor Red
    $backupPath = Read-Host "Ingresa la ruta completa del backup"
    
    if (-not (Test-Path $backupPath)) {
        Write-Host "Ruta invalida" -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit
    }
}

Write-Host "Backup encontrado" -ForegroundColor Green
Write-Host ""

# Credenciales de Supabase
Write-Host "Credenciales de Supabase:" -ForegroundColor Yellow
Write-Host "(Settings -> Database -> Connection Info)" -ForegroundColor Gray
Write-Host ""

$supabaseHost = Read-Host "Host de Supabase (ej: db.abc123.supabase.co)"

# Limpiar caracteres extraños y espacios
$supabaseHost = $supabaseHost.Trim()
$supabaseHost = $supabaseHost -replace "[^\x00-\x7F]", ""
$supabaseHost = $supabaseHost -replace "https?://", ""
$supabaseHost = $supabaseHost -replace "/$", ""
$supabaseHost = $supabaseHost -replace "\s+", ""

# Si solo pego el nombre del proyecto, agregar db.
if ($supabaseHost -notlike "db.*" -and $supabaseHost -notlike "*pooler*" -and $supabaseHost -notlike "*aws-*") {
    $supabaseHost = "db.$supabaseHost"
}

$supabasePassword = Read-Host "Contrasena de la base de datos" -AsSecureString
$plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($supabasePassword))

Write-Host ""
Write-Host "Host que se usara: $supabaseHost" -ForegroundColor Cyan
Write-Host ""

$confirm = Read-Host "Continuar con la importacion? (s/n)"

if ($confirm -ne "s") {
    Write-Host "Operacion cancelada" -ForegroundColor Yellow
    exit
}

Write-Host ""
Write-Host "Importando backup a Supabase..." -ForegroundColor Yellow
Write-Host "Esto puede tardar varios minutos..." -ForegroundColor Gray
Write-Host ""

# Configurar password
$env:PGPASSWORD = $plainPassword

try {
    $arguments = @(
        "-h", $supabaseHost,
        "-p", "5432",
        "-U", "postgres",
        "-d", "postgres",
        "--no-owner",
        "--no-acl",
        "--clean",
        "--if-exists",
        "-v",
        $backupPath
    )
    
    Write-Host "Comando: pg_restore -h $supabaseHost -U postgres -d postgres ..." -ForegroundColor Gray
    Write-Host ""
    
    & $pgRestorePath @arguments 2>&1 | Tee-Object -Variable output
    
    Write-Host ""
    
    if ($output -like "*ERROR*" -and $output -notlike "*already exists*" -and $output -notlike "*does not exist*") {
        Write-Host "Se encontraron errores" -ForegroundColor Yellow
    } else {
        Write-Host "Importacion completada" -ForegroundColor Green
    }
    
    Write-Host ""
    Write-Host "=== Proximos pasos ===" -ForegroundColor Cyan
    Write-Host "1. Ve a Supabase -> Table Editor y verifica las tablas" -ForegroundColor White
    Write-Host "2. Configura Laravel: .\CONFIGURAR_SUPABASE.ps1" -ForegroundColor White
    Write-Host "3. Prueba: php artisan tinker" -ForegroundColor White
    Write-Host "   >>> User::count()" -ForegroundColor Gray
    Write-Host ""
    
} catch {
    Write-Host ""
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
} finally {
    Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue
}

Write-Host ""
Read-Host "Presiona Enter para salir"
