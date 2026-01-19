# Script para Importar Datos a Supabase
# Ejecuta este script DESPUÉS de exportar desde Render

Write-Host "=== Importación de Base de Datos a Supabase ===" -ForegroundColor Cyan
Write-Host ""

# Verificar que psql esté instalado
$psqlPath = Get-Command psql -ErrorAction SilentlyContinue

if (-not $psqlPath) {
    Write-Host "❌ psql no está instalado" -ForegroundColor Red
    Write-Host ""
    Write-Host "Para instalar PostgreSQL en Windows:" -ForegroundColor Yellow
    Write-Host "1. Descarga PostgreSQL desde: https://www.postgresql.org/download/windows/" -ForegroundColor Gray
    Write-Host "2. Instala y asegúrate de marcar 'Command Line Tools'" -ForegroundColor Gray
    Write-Host "3. O usa: winget install PostgreSQL.PostgreSQL" -ForegroundColor Gray
    Write-Host ""
    Read-Host "Presiona Enter para salir"
    exit
}

# Buscar archivos de backup
$backupFolder = "database_backups"
if (Test-Path $backupFolder) {
    $backupFiles = Get-ChildItem -Path $backupFolder -Filter "*.sql" | Sort-Object LastWriteTime -Descending
    
    if ($backupFiles.Count -gt 0) {
        Write-Host "Archivos de backup encontrados:" -ForegroundColor Green
        Write-Host ""
        for ($i = 0; $i -lt $backupFiles.Count; $i++) {
            $file = $backupFiles[$i]
            $size = [math]::Round($file.Length / 1MB, 2)
            Write-Host "  [$($i + 1)] $($file.Name) - $size MB - $($file.LastWriteTime)" -ForegroundColor Cyan
        }
        Write-Host ""
        
        $selection = Read-Host "Selecciona el número del archivo a importar (Enter para el más reciente)"
        
        if ([string]::IsNullOrWhiteSpace($selection)) {
            $backupFile = $backupFiles[0].FullName
        } else {
            $index = [int]$selection - 1
            if ($index -ge 0 -and $index -lt $backupFiles.Count) {
                $backupFile = $backupFiles[$index].FullName
            } else {
                Write-Host "❌ Selección inválida" -ForegroundColor Red
                Read-Host "Presiona Enter para salir"
                exit
            }
        }
    } else {
        Write-Host "❌ No se encontraron archivos de backup en $backupFolder" -ForegroundColor Red
        $backupFile = Read-Host "Ingresa la ruta completa del archivo SQL a importar"
    }
} else {
    Write-Host "⚠️  Carpeta $backupFolder no encontrada" -ForegroundColor Yellow
    $backupFile = Read-Host "Ingresa la ruta completa del archivo SQL a importar"
}

if (-not (Test-Path $backupFile)) {
    Write-Host "❌ Archivo no encontrado: $backupFile" -ForegroundColor Red
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host ""
Write-Host "Archivo seleccionado: $backupFile" -ForegroundColor Green
Write-Host ""

Write-Host "Por favor, proporciona la información de tu proyecto Supabase:" -ForegroundColor Yellow
Write-Host "(Puedes encontrarla en: Settings -> Database en tu proyecto de Supabase)" -ForegroundColor Gray
Write-Host ""

$supabaseHost = Read-Host "Host de Supabase (ej: abc123.supabase.co)"
$supabasePassword = Read-Host "Contraseña de la base de datos" -AsSecureString
$plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($supabasePassword))

Write-Host ""
Write-Host "⚠️  ADVERTENCIA: Esta operación importará datos a Supabase" -ForegroundColor Yellow
Write-Host "Si ya tienes datos en Supabase, podrían producirse conflictos" -ForegroundColor Yellow
Write-Host ""

$confirm = Read-Host "¿Continuar? (s/n)"

if ($confirm -ne "s" -and $confirm -ne "S") {
    Write-Host "Operación cancelada" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host ""
Write-Host "Importando base de datos..." -ForegroundColor Yellow
Write-Host ""

# Configurar la variable de entorno para la contraseña
$env:PGPASSWORD = $plainPassword

try {
    # Ejecutar psql
    $arguments = @(
        "-h", $supabaseHost,
        "-p", "5432",
        "-U", "postgres",
        "-d", "postgres",
        "-f", $backupFile
    )
    
    & psql @arguments 2>&1 | Tee-Object -Variable output
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "✅ Importación completada exitosamente" -ForegroundColor Green
        Write-Host ""
        
        Write-Host "=== Próximos pasos ===" -ForegroundColor Yellow
        Write-Host "1. Verifica los datos en Supabase Studio" -ForegroundColor White
        Write-Host "2. Actualiza tu archivo .env con las credenciales de Supabase" -ForegroundColor White
        Write-Host "   (ejecuta: .\CONFIGURAR_SUPABASE.ps1)" -ForegroundColor Gray
        Write-Host "3. Prueba la aplicación localmente" -ForegroundColor White
        Write-Host "4. Actualiza las variables de entorno en producción" -ForegroundColor White
        Write-Host ""
    } else {
        Write-Host ""
        Write-Host "❌ Error durante la importación" -ForegroundColor Red
        Write-Host ""
        Write-Host "Errores comunes:" -ForegroundColor Yellow
        Write-Host "- Restricciones de clave foránea: Los datos deben importarse en orden" -ForegroundColor Gray
        Write-Host "- Tablas ya existentes: Usa --clean en pg_dump o elimina las tablas manualmente" -ForegroundColor Gray
        Write-Host "- Permisos: Asegúrate de usar el usuario 'postgres'" -ForegroundColor Gray
        Write-Host ""
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
