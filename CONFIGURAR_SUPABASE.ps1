# Script de Migración a Supabase
# Este script te ayuda a configurar la conexión a Supabase

Write-Host "=== Migración de Base de Datos a Supabase ===" -ForegroundColor Cyan
Write-Host ""

# Verificar si existe archivo .env
if (-not (Test-Path ".env")) {
    Write-Host "❌ No se encontró archivo .env" -ForegroundColor Red
    Write-Host "Copiando .env.example a .env..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    Write-Host "✅ Archivo .env creado" -ForegroundColor Green
}

Write-Host ""
Write-Host "Por favor, proporciona la información de tu proyecto Supabase:" -ForegroundColor Yellow
Write-Host "(Puedes encontrarla en: Settings -> Database en tu proyecto de Supabase)" -ForegroundColor Gray
Write-Host ""

# Solicitar información
$supabaseHost = Read-Host "Host de Supabase (ej: abc123.supabase.co)"
$supabasePassword = Read-Host "Contraseña de la base de datos" -AsSecureString
$plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($supabasePassword))

# Leer archivo .env
$envContent = Get-Content ".env" -Raw

# Actualizar configuraciones
$envContent = $envContent -replace "DB_CONNECTION=.*", "DB_CONNECTION=pgsql"
$envContent = $envContent -replace "DB_HOST=.*", "DB_HOST=$supabaseHost"
$envContent = $envContent -replace "DB_PORT=.*", "DB_PORT=5432"
$envContent = $envContent -replace "DB_DATABASE=.*", "DB_DATABASE=postgres"
$envContent = $envContent -replace "DB_USERNAME=.*", "DB_USERNAME=postgres"
$envContent = $envContent -replace "DB_PASSWORD=.*", "DB_PASSWORD=$plainPassword"

# Agregar o actualizar DB_SSLMODE
if ($envContent -match "DB_SSLMODE=") {
    $envContent = $envContent -replace "DB_SSLMODE=.*", "DB_SSLMODE=require"
} else {
    $envContent += "`nDB_SSLMODE=require"
}

# Agregar DATABASE_URL si no existe
$databaseUrl = "postgresql://postgres:$plainPassword@$supabaseHost:5432/postgres?sslmode=require"
if ($envContent -notmatch "DATABASE_URL=") {
    $envContent += "`nDATABASE_URL=$databaseUrl"
} else {
    $envContent = $envContent -replace "DATABASE_URL=.*", "DATABASE_URL=$databaseUrl"
}

# Guardar cambios
Set-Content ".env" $envContent -NoNewline

Write-Host ""
Write-Host "✅ Configuración actualizada exitosamente" -ForegroundColor Green
Write-Host ""

# Probar conexión
Write-Host "Probando conexión a la base de datos..." -ForegroundColor Yellow
Write-Host ""

try {
    $testOutput = php artisan migrate:status 2>&1
    Write-Host "✅ Conexión exitosa a Supabase" -ForegroundColor Green
    Write-Host ""
    Write-Host "Estado de migraciones:" -ForegroundColor Cyan
    Write-Host $testOutput
} catch {
    Write-Host "❌ Error al conectar con la base de datos" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    Write-Host "Verifica que:" -ForegroundColor Yellow
    Write-Host "  1. El host de Supabase sea correcto" -ForegroundColor Gray
    Write-Host "  2. La contraseña sea correcta" -ForegroundColor Gray
    Write-Host "  3. Tu IP esté permitida en Supabase (Settings -> Database -> Connection Pooling)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "=== Próximos pasos ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Si la conexión fue exitosa, ejecuta las migraciones:" -ForegroundColor White
Write-Host "   php artisan migrate" -ForegroundColor Gray
Write-Host ""
Write-Host "2. O si quieres empezar desde cero:" -ForegroundColor White
Write-Host "   php artisan migrate:fresh --seed" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Para importar datos desde Render, consulta MIGRACION_SUPABASE.md" -ForegroundColor White
Write-Host ""

Read-Host "Presiona Enter para salir"
