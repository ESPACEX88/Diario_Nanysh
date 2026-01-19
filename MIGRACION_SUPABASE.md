# Guía de Migración de Render a Supabase

## Pasos para Migrar tu Base de Datos

### 1. Crear Proyecto en Supabase

1. Ve a [https://supabase.com](https://supabase.com) e inicia sesión
2. Haz clic en "New Project"
3. Completa los datos:
   - **Name**: Diario de Nahysh (o el nombre que prefieras)
   - **Database Password**: Crea una contraseña segura (¡guárdala!)
   - **Region**: Selecciona la región más cercana
   - **Plan**: Free (o el que prefieras)
4. Espera unos 2 minutos mientras se crea el proyecto

### 2. Obtener Credenciales de Conexión

1. En tu proyecto de Supabase, ve a **Settings** → **Database**
2. En la sección "Connection Info", encontrarás:
   - **Host**: `[tu-proyecto].supabase.co`
   - **Database name**: `postgres`
   - **Port**: `5432`
   - **User**: `postgres`
   - **Password**: La que creaste en el paso 1

3. También puedes copiar la **Connection String** completa en la pestaña "Connection string" → "URI"
   - Formato: `postgresql://postgres:[PASSWORD]@[HOST]:5432/postgres`

### 3. Exportar Datos de Render (si tienes datos)

#### Opción A: Usando pg_dump (Recomendado)
```bash
# Conéctate a tu base de datos de Render y exporta
pg_dump -h [RENDER_HOST] -U [RENDER_USER] -d diario_nahysh -F c -b -v -f backup_render.dump

# O exporta como SQL
pg_dump -h [RENDER_HOST] -U [RENDER_USER] -d diario_nahysh > backup_render.sql
```

#### Opción B: Desde el Panel de Render
1. Ve a tu base de datos en Render
2. Busca la opción de backup/export
3. Descarga el backup

### 4. Importar Datos a Supabase

#### Opción A: Si tienes el archivo .dump
```bash
pg_restore -h [SUPABASE_HOST] -U postgres -d postgres -v backup_render.dump
```

#### Opción B: Si tienes el archivo .sql
```bash
psql -h [SUPABASE_HOST] -U postgres -d postgres -f backup_render.sql
```

#### Opción C: Usar Supabase Studio (para bases de datos pequeñas)
1. Ve a **Database** → **Tables** en Supabase
2. Usa el editor SQL para ejecutar tus scripts de migración

### 5. Actualizar Configuración de Laravel

Actualiza tu archivo `.env` con las credenciales de Supabase:

```env
DB_CONNECTION=pgsql
DB_HOST=[tu-proyecto].supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[tu-contraseña-supabase]
DB_SSLMODE=require

# Alternativamente, usa la conexión directa:
# DATABASE_URL=postgresql://postgres:[PASSWORD]@[HOST]:5432/postgres?sslmode=require
```

### 6. Ejecutar Migraciones (si es una BD nueva)

Si NO importaste datos y quieres crear las tablas desde cero:

```bash
php artisan migrate:fresh --seed
```

Si importaste datos pero quieres asegurarte que las migraciones estén actualizadas:

```bash
php artisan migrate
```

### 7. Actualizar Variables de Entorno en Producción

Si despliegas en algún servicio (Vercel, Railway, etc.), actualiza las variables de entorno:

```env
DB_CONNECTION=pgsql
DB_HOST=[tu-proyecto].supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[tu-contraseña-supabase]
DB_SSLMODE=require
```

### 8. Probar la Conexión

```bash
# Prueba la conexión localmente
php artisan migrate:status

# O prueba con tinker
php artisan tinker
>>> DB::connection()->getPdo();
```

## Ventajas de Supabase

✅ **Mejor rendimiento**: Supabase suele ser más rápido
✅ **Más características**: Auth, Storage, Realtime incluidos
✅ **Mejor panel de administración**: Supabase Studio es excelente
✅ **Backups automáticos**: En planes pagos
✅ **PostgreSQL optimizado**: Extensiones útiles preinstaladas
✅ **API REST automática**: Puedes usar la API de Supabase si quieres

## Comandos Útiles de PostgreSQL

```bash
# Listar todas las tablas
psql -h [HOST] -U postgres -d postgres -c "\dt"

# Verificar tamaño de la BD
psql -h [HOST] -U postgres -d postgres -c "SELECT pg_size_pretty(pg_database_size('postgres'));"

# Backup de una tabla específica
pg_dump -h [HOST] -U postgres -d postgres -t [nombre_tabla] > tabla_backup.sql
```

## Solución de Problemas

### Error de SSL
Si obtienes errores de SSL, asegúrate de tener `DB_SSLMODE=require` en tu `.env`

### Timeout de Conexión
Supabase tiene un timeout de 10 segundos. Si tienes consultas lentas, optimízalas o usa la API de Supabase.

### Límites del Plan Free
- 500 MB de espacio de base de datos
- Pausa después de 7 días de inactividad
- 2 GB de transferencia de datos/mes

## Recursos Adicionales

- [Documentación de Supabase](https://supabase.com/docs)
- [Guía de Migración de Supabase](https://supabase.com/docs/guides/migrations)
- [PostgreSQL con Laravel](https://laravel.com/docs/database#postgresql)
