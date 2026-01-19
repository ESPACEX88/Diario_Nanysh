# Script para Exportar Datos desde Render
# Ejecuta este script ANTES de cambiar a Supabase para hacer backup

Write-Host "=== Exportación de Base de Datos desde Render ===" -ForegroundColor Cyan
Write-Host ""

# Verificar que pg_dump esté instalado
$pgDumpPath = Get-Command pg_dump -ErrorAction SilentlyContinue

if (-not $pgDumpPath) {
    Write-Host "❌ pg_dump no está instalado" -ForegroundColor Red
    Write-Host ""
    Write-Host "Para instalar PostgreSQL en Windows:" -ForegroundColor Yellow
    Write-Host "1. Descarga PostgreSQL desde: https://www.postgresql.org/download/windows/" -ForegroundColor Gray
    Write-Host "2. Instala y asegúrate de marcar 'Command Line Tools'" -ForegroundColor Gray
    Write-Host "3. O usa: winget install PostgreSQL.PostgreSQL" -ForegroundColor Gray
    Write-Host ""
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host "Por favor, proporciona la información de tu base de datos en Render:" -ForegroundColor Yellow
Write-Host "(Puedes encontrarla en el panel de Render -> Database -> Connections)" -ForegroundColor Gray
Write-Host ""

$renderHost = Read-Host "Host de Render (ej: dpg-xxxxx-a.oregon-postgres.render.com)"
$renderPort = Read-Host "Puerto (normalmente 5432)"
$renderDatabase = Read-Host "Nombre de la base de datos"
$renderUser = Read-Host "Usuario"
$renderPassword = Read-Host "Contraseña" -AsSecureString
$plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($renderPassword))

# Crear carpeta de backups si no existe
$backupFolder = "database_backups"
if (-not (Test-Path $backupFolder)) {
    New-Item -ItemType Directory -Path $backupFolder | Out-Null
}

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupFile = "$backupFolder\render_backup_$timestamp.sql"

Write-Host ""
Write-Host "Exportando base de datos..." -ForegroundColor Yellow
Write-Host "Archivo: $backupFile" -ForegroundColor Gray
Write-Host ""

# Configurar la variable de entorno para la contraseña
$env:PGPASSWORD = $plainPassword

try {
    # Ejecutar pg_dump
    $arguments = @(
        "-h", $renderHost,
        "-p", $renderPort,
        "-U", $renderUser,
        "-d", $renderDatabase,
        "-f", $backupFile,
        "--no-owner",
        "--no-acl",
        "-v"
    )
    
    & pg_dump @arguments 2>&1 | Tee-Object -Variable output
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "✅ Exportación completada exitosamente" -ForegroundColor Green
        Write-Host ""
        
        $fileSize = (Get-Item $backupFile).Length / 1MB
        Write-Host "📁 Archivo creado: $backupFile" -ForegroundColor Cyan
        Write-Host "📊 Tamaño: $([math]::Round($fileSize, 2)) MB" -ForegroundColor Cyan
        Write-Host ""
        
        Write-Host "=== Próximos pasos ===" -ForegroundColor Yellow
        Write-Host "1. Configura Supabase ejecutando: .\CONFIGURAR_SUPABASE.ps1" -ForegroundColor White
        Write-Host "2. Importa el backup a Supabase:" -ForegroundColor White
        Write-Host "   psql -h [SUPABASE_HOST] -U postgres -d postgres -f $backupFile" -ForegroundColor Gray
        Write-Host ""
    } else {
        Write-Host ""
        Write-Host "❌ Error durante la exportación" -ForegroundColor Red
        Write-Host $output -ForegroundColor Red
    }
} catch {
    Write-Host ""
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
} finally {
    # Limpiar la variable de contraseña
    Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue
}

Write-Host ""
Read-Host "Presiona Enter para salir"
