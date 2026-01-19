# CONFIGURA ESTAS VARIABLES DIRECTAMENTE AQUI:
$SUPABASE_HOST = "db.shaqukxfvhtwngqizzkx.supabase.co"
$SUPABASE_PASSWORD = "Sksakmak47.#"
$BACKUP_PATH = "C:\Users\posad\Downloads\2026-01-19T16_30Z.dir\2026-01-19T16_30Z\diario_fhd4"

# ============================================
# NO EDITES NADA DEBAJO DE ESTA LINEA
# ============================================

Write-Host "=== Importacion de Backup de Render a Supabase ===" -ForegroundColor Cyan
Write-Host ""

# Buscar PostgreSQL
$pgRestorePath = $null
$pgVersions = @(
    "C:\Program Files\PostgreSQL\17\bin\pg_restore.exe",
    "C:\Program Files\PostgreSQL\16\bin\pg_restore.exe"
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
    Write-Host "PostgreSQL 16/17 no encontrado" -ForegroundColor Red
    Write-Host "Instalando PostgreSQL 17..." -ForegroundColor Yellow
    winget install PostgreSQL.PostgreSQL.17
    Write-Host "Ejecuta este script nuevamente" -ForegroundColor Yellow
    Read-Host "Presiona Enter"
    exit
}

# Verificar backup
if (-not (Test-Path $BACKUP_PATH)) {
    Write-Host "Backup no encontrado en: $BACKUP_PATH" -ForegroundColor Red
    Read-Host "Presiona Enter"
    exit
}

Write-Host "Backup encontrado" -ForegroundColor Green
Write-Host ""

Write-Host "Configuracion:" -ForegroundColor Yellow
Write-Host "  Host: $SUPABASE_HOST" -ForegroundColor Gray
Write-Host "  Database: postgres" -ForegroundColor Gray
Write-Host "  User: postgres" -ForegroundColor Gray
Write-Host ""

$confirm = Read-Host "Continuar con la importacion? (s/n)"
if ($confirm -ne "s") {
    exit
}

Write-Host ""
Write-Host "Importando... esto puede tardar varios minutos" -ForegroundColor Yellow
Write-Host ""

$env:PGPASSWORD = $SUPABASE_PASSWORD

try {
    $pinfo = New-Object System.Diagnostics.ProcessStartInfo
    $pinfo.FileName = $pgRestorePath
    $pinfo.RedirectStandardError = $true
    $pinfo.RedirectStandardOutput = $true
    $pinfo.UseShellExecute = $false
    $pinfo.Arguments = "-h $SUPABASE_HOST -p 5432 -U postgres -d postgres --no-owner --no-acl --clean --if-exists -v `"$BACKUP_PATH`""
    
    $process = New-Object System.Diagnostics.Process
    $process.StartInfo = $pinfo
    $process.Start() | Out-Null
    
    $stdout = $process.StandardOutput.ReadToEnd()
    $stderr = $process.StandardError.ReadToEnd()
    
    $process.WaitForExit()
    
    Write-Host $stdout
    Write-Host $stderr -ForegroundColor Yellow
    
    Write-Host ""
    if ($process.ExitCode -eq 0) {
        Write-Host "Importacion completada exitosamente" -ForegroundColor Green
    } else {
        Write-Host "Importacion completada con advertencias" -ForegroundColor Yellow
        Write-Host "Verifica en Supabase si los datos estan presentes" -ForegroundColor Yellow
    }
    
    Write-Host ""
    Write-Host "=== Proximos pasos ===" -ForegroundColor Cyan
    Write-Host "1. Ve a Supabase -> Table Editor" -ForegroundColor White
    Write-Host "2. Verifica que las tablas tengan datos" -ForegroundColor White
    Write-Host "3. Ejecuta: php artisan migrate:status" -ForegroundColor White
    Write-Host ""
    
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
} finally {
    Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue
}

Read-Host "Presiona Enter para salir"
