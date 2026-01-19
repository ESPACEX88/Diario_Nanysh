# Script para importar backup de Render a Supabase
# Específico para el backup en formato .dir

Write-Host "=== Importación de Backup de Render a Supabase ===" -ForegroundColor Cyan
Write-Host ""

# Buscar pg_restore en ubicaciones comunes
$pgRestorePath = $null
$commonPaths = @(
    "C:\Program Files\PostgreSQL\15\bin\pg_restore.exe",
    "C:\Program Files\PostgreSQL\16\bin\pg_restore.exe",
    "C:\Program Files\PostgreSQL\17\bin\pg_restore.exe",
    "C:\Program Files (x86)\PostgreSQL\15\bin\pg_restore.exe",
    "C:\Program Files (x86)\PostgreSQL\16\bin\pg_restore.exe"
)

# Primero intentar encontrar en PATH
$pgRestoreCmd = Get-Command pg_restore -ErrorAction SilentlyContinue
if ($pgRestoreCmd) {
    $pgRestorePath = $pgRestoreCmd.Source
    Write-Host "✅ pg_restore encontrado en PATH" -ForegroundColor Green
} else {
    # Buscar en ubicaciones comunes
    foreach ($path in $commonPaths) {
        if (Test-Path $path) {
            $pgRestorePath = $path
            Write-Host "✅ pg_restore encontrado en: $path" -ForegroundColor Green
            break
        }
    }
}

if (-not $pgRestorePath) {
    Write-Host "❌ pg_restore no encontrado" -ForegroundColor Red
    Write-Host ""
    Write-Host "Instalando PostgreSQL..." -ForegroundColor Yellow
    Write-Host ""
    
    try {
        winget install PostgreSQL.PostgreSQL.15
        Write-Host ""
        Write-Host "✅ PostgreSQL instalado" -ForegroundColor Green
        Write-Host ""
        Write-Host "⚠️  IMPORTANTE: Cierra COMPLETAMENTE VS Code y vuelve a abrirlo" -ForegroundColor Yellow
        Write-Host "Luego ejecuta este script nuevamente" -ForegroundColor Yellow
        Read-Host "Presiona Enter para salir"
        exit
    } catch {
        Write-Host "❌ Error al instalar PostgreSQL" -ForegroundColor Red
        Write-Host "Instala manualmente desde: https://www.postgresql.org/download/windows/" -ForegroundColor Yellow
        Read-Host "Presiona Enter para salir"
        exit
    }
}

Write-Host ""

# Ruta del backup
$backupPath = "C:\Users\posad\Downloads\2026-01-19T16_30Z.dir\2026-01-19T16_30Z\diario_fhd4"

Write-Host "Verificando backup en: $backupPath" -ForegroundColor Yellow
Write-Host ""

if (-not (Test-Path $backupPath)) {
    Write-Host "❌ No se encontró el backup en la ruta especificada" -ForegroundColor Red
    Write-Host "Ruta buscada: $backupPath" -ForegroundColor Gray
    Write-Host ""
    
    $customPath = Read-Host "Ingresa la ruta completa del backup (carpeta diario_fhd4)"
    
    if (Test-Path $customPath) {
        $backupPath = $customPath
    } else {
        Write-Host "❌ Ruta inválida" -ForegroundColor Red
        Read-Host "Presiona Enter para salir"
        exit
    }
}

Write-Host "✅ Backup encontrado" -ForegroundColor Green
Write-Host ""

# Obtener credenciales de Supabase
Write-Host "Ingresa las credenciales de Supabase:" -ForegroundColor Yellow
Write-Host "(Settings → Database → Connection Info)" -ForegroundColor Gray
Write-Host ""

$supabaseHost = Read-Host "Host de Supabase (ej: db.abc123xyz.supabase.co)"
$supabasePassword = Read-Host "Contraseña de la base de datos" -AsSecureString
$plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($supabasePassword))

Write-Host ""
Write-Host "⚠️  IMPORTANTE:" -ForegroundColor Yellow
Write-Host "Esta operación importará TODOS los datos del backup a Supabase" -ForegroundColor Yellow
Write-Host "Si ya tienes tablas en Supabase, se crearán conflictos" -ForegroundColor Yellow
Write-Host ""

$confirm = Read-Host "¿Continuar con la importación? (s/n)"

if ($confirm -ne "s" -and $confirm -ne "S") {
    Write-Host "Operación cancelada" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host ""
Write-Host "Importando backup a Supabase..." -ForegroundColor Yellow
Write-Host "Esto puede tardar varios minutos dependiendo del tamaño de la base de datos..." -ForegroundColor Gray
Write-Host ""

# Configurar la variable de entorno para la contraseña
$env:PGPASSWORD = $plainPassword

try {
    # Ejecutar pg_restore con la ruta completa
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
    
    Write-Host "Ejecutando: pg_restore -h $supabaseHost -U postgres -d postgres --no-owner --no-acl --clean --if-exists -v $backupPath" -ForegroundColor Gray
    Write-Host ""
    
    & $pgRestorePath @arguments 2>&1 | Tee-Object -Variable output
    
    # pg_restore puede devolver exit code distinto de 0 aunque sea exitoso
    # debido a warnings, así que verificamos el output
    
    if ($output -like "*ERROR*" -and $output -notlike "*already exists*") {
        Write-Host ""
        Write-Host "⚠️  Se encontraron algunos errores durante la importación" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Errores comunes que puedes ignorar:" -ForegroundColor Cyan
        Write-Host "- 'role does not exist': Normal, los roles de Render no existen en Supabase" -ForegroundColor Gray
        Write-Host "- 'already exists': Normal si ejecutaste esto más de una vez" -ForegroundColor Gray
        Write-Host ""
        Write-Host "Verifica en Supabase (Table Editor) si tus datos están presentes" -ForegroundColor Yellow
    } else {
        Write-Host ""
        Write-Host "✅ Importación completada" -ForegroundColor Green
    }
    
    Write-Host ""
    Write-Host "=== Próximos pasos ===" -ForegroundColor Cyan
    Write-Host "1. Ve a Supabase → Table Editor y verifica que las tablas tengan datos" -ForegroundColor White
    Write-Host "2. Ejecuta: .\CONFIGURAR_SUPABASE.ps1 para actualizar tu .env" -ForegroundColor White
    Write-Host "3. Prueba tu aplicación localmente con:" -ForegroundColor White
    Write-Host "   php artisan migrate:status" -ForegroundColor Gray
    Write-Host "   php artisan tinker" -ForegroundColor Gray
    Write-Host "   >>> User::count()" -ForegroundColor Gray
    Write-Host ""
    
} catch {
    Write-Host ""
    Write-Host "❌ Error durante la importación: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Soluciones:" -ForegroundColor Yellow
    Write-Host "1. Verifica que el host y la contraseña de Supabase sean correctos" -ForegroundColor Gray
    Write-Host "2. Asegúrate de que PostgreSQL esté instalado correctamente" -ForegroundColor Gray
    Write-Host "3. Verifica tu conexión a internet" -ForegroundColor Gray
} finally {
    # Limpiar la variable de contraseña
    Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue
}

Write-Host ""
Read-Host "Presiona Enter para salir"
