# Configuración Render + Supabase - Guía Completa

## 📋 Resumen de tu Arquitectura

- **Hosting**: Render (aplicación Laravel)
- **Base de Datos**: Supabase (PostgreSQL gestionado)
- **Conexión**: HTTPS con SSL requerido

---

## 🔧 Paso 1: Obtener Credenciales de Supabase

### 1.1 Ir al Dashboard de Supabase
1. Entra a [https://supabase.com](https://supabase.com)
2. Selecciona tu proyecto

### 1.2 Obtener Connection String
1. Ve a **Settings** → **Database**
2. En la sección **"Connection string"**:
   - Selecciona **"URI"** tab
   - Copia el connection string completo
   
   Formato: `postgresql://postgres:[PASSWORD]@[PROJECT_ID].supabase.co:5432/postgres?sslmode=require`

### 1.3 Datos Individuales (alternativo)
En **Connection Info** encontrarás:
- **Host**: `[tu-project-id].supabase.co`
- **Port**: `5432` (directo) o `6543` (pooler - RECOMENDADO)
- **Database**: `postgres`
- **User**: `postgres`
- **Password**: La que creaste al iniciar el proyecto

> ⚠️ **IMPORTANTE**: Usa el puerto **6543** (Supavisor) para production en Render. Esto permite manejar múltiples conexiones simultáneas sin agotar los límites.

---

## 🚀 Paso 2: Configurar Variables en Render

### 2.1 Ir al Dashboard de Render
1. Entra a [https://render.com](https://render.com)
2. Selecciona tu servicio/web service

### 2.2 Agregar Variables de Entorno
Ve a **Environment** y agrega/edita estas variables:

```env
# ===========================================
# CONFIGURACIÓN SUPABASE PARA RENDER
# ===========================================

APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Database - Supabase Connection
DB_CONNECTION=pgsql
DB_HOST=[tu-project-id].supabase.co
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=tu-contraseña-supabase
DB_SSLMODE=require

# O usa la URL completa (recomendado para Render)
DATABASE_URL=postgresql://postgres:[PASSWORD]@[PROJECT_ID].supabase.co:6543/postgres?sslmode=require

# Session & Cache (usando database por simplicidad)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Log level para production
LOG_CHANNEL=errorlog
LOG_LEVEL=info

# Cloudinary (si usas almacenamiento de imágenes)
CLOUDINARY_CLOUD_NAME=tu-cloud-name
CLOUDINARY_API_KEY=tu-api-key
CLOUDINARY_API_SECRET=tu-api-secret
CLOUDINARY_URL=cloudinary://api-key:api-secret@cloud-name
```

### 2.3 Reemplazar Valores
Reemplaza estos valores con tus datos reales:
- `[tu-project-id]` → El ID de tu proyecto en Supabase (ej: `abcxyz123`)
- `tu-contraseña-supabase` → Tu contraseña de PostgreSQL
- `tu-dominio.com` → Tu dominio real en Render

---

## 🔐 Paso 3: Consideraciones de Seguridad

### 3.1 Service Role Key vs Anon Key
Si planeas usar características de Supabase más allá de la DB:

```env
# Solo si necesitas autenticación de Supabase o RLS
SUPABASE_URL=https://[PROJECT_ID].supabase.co
SUPABASE_ANON_KEY=tu-anon-key
SUPABASE_SERVICE_ROLE_KEY=tu-service-role-key
```

> ⚠️ **NUNCA** expongas el `SERVICE_ROLE_KEY` en el frontend. Solo úsalo en el backend de Laravel.

### 3.2 Row Level Security (RLS)
Como estás usando Laravel Auth (no Supabase Auth):

**Opción A - Usar Service Role Key (Recomendado)**
- Laravel se conecta con `service_role_key` que bypassa RLS automáticamente
- Mantén el control de permisos en Laravel

**Opción B - Deshabilitar RLS**
Si no necesitas RLS, deshabilítalo en cada tabla:
```sql
ALTER TABLE users DISABLE ROW LEVEL SECURITY;
ALTER TABLE posts DISABLE ROW LEVEL SECURITY;
-- etc...
```

---

## ⚡ Paso 4: Optimizaciones Específicas para Render + Supabase

### 4.1 Connection Pooling (CRÍTICO)
Render tiene límites de conexiones. Configura correctamente:

```env
# Puerto 6543 usa Supavisor (pooler incluido)
DB_PORT=6543

# Ajusta pool de conexiones en config/database.php
# (ya está configurado, pero verifica)
```

### 4.2 Timeout Settings
Agrega estas variables para evitar timeouts:

```env
# Aumentar timeout de conexión
DB_CONNECT_TIMEOUT=30
DB_READ_WRITE_TIMEOUT=60

# Laravel HTTP Client timeout
HTTP_CLIENT_TIMEOUT=30
```

### 4.3 Persistent Connections
Para mejor rendimiento, habilita conexiones persistentes:

Edita `config/database.php` en la conexión `pgsql`:
```php
'pgsql' => [
    'driver' => 'pgsql',
    // ... otras configuraciones
    'options' => [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ],
],
```

---

## 🧪 Paso 5: Verificar Conexión

### 5.1 Desde Render (Logs)
Después de guardar las variables:

1. Ve a **Logs** en Render
2. Reinicia el servicio (Deploy → Manual Deploy)
3. Busca logs de conexión exitosa

### 5.2 Comandos de Verificación
Ejecuta estos comandos vía SSH o localmente:

```bash
# Verificar migraciones
php artisan migrate:status

# Probar conexión directa
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::table('users')->count();

# Ver información de la DB
php artisan db:show
```

### 5.3 Health Check Endpoint
Crea un endpoint para monitoreo:

```php
// routes/api.php
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
            'timestamp' => now()->toIso8601String()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'database' => 'disconnected',
            'error' => $e->getMessage()
        ], 500);
    }
});
```

Accede a: `https://tu-dominio.com/api/health`

---

## 📊 Paso 6: Monitoreo y Debugging

### 6.1 En Supabase Dashboard
1. Ve a **Database** → **Query Performance**
2. Revisa queries lentas
3. Monitorea conexiones activas

### 6.2 En Render Dashboard
1. **Metrics** → Ver uso de memoria y CPU
2. **Logs** → Buscar errores de conexión
3. **Events** → Ver deploys y reinicios

### 6.3 Logs Comunes y Soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| `SSL connection required` | Falta SSL | Agrega `DB_SSLMODE=require` |
| `Connection timeout` | Puerto incorrecto | Usa `DB_PORT=6543` (no 5432) |
| `Too many connections` | Sin pooling | Usa puerto 6543 + persistent connections |
| `Authentication failed` | Password incorrecto | Verifica password en Supabase |
| `could not translate host name` | Host mal escrito | Verifica `[project-id].supabase.co` |

---

## 🔄 Paso 7: Deploy Continuo

### 7.1 Auto-Deploy en Render
Render hace auto-deploy cuando:
- Haces push a GitHub/GitLab
- Cambias variables de entorno
- Haces deploy manual

### 7.2 Ejecutar Migraciones Automáticamente
Agrega esto en tu `render.yaml` o comando de start:

```yaml
# render.yaml
services:
  - type: web
    name: mi-app-laravel
    env: node
    buildCommand: |
      composer install --no-dev --optimize-autoloader
      php artisan migrate --force
      php artisan config:cache
      php artisan route:cache
      php artisan view:cache
    startCommand: php artisan serve --host=0.0.0.0 --port=${PORT}
```

O en el comando de inicio de Render:
```bash
php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=$PORT
```

---

## ✅ Checklist Final

- [ ] Proyecto creado en Supabase
- [ ] Connection string copiado
- [ ] Variables actualizadas en Render
- [ ] Puerto cambiado a 6543
- [ ] SSL habilitado (`DB_SSLMODE=require`)
- [ ] Migraciones ejecutadas (`php artisan migrate --force`)
- [ ] Health check funcionando
- [ ] Logs verificados sin errores
- [ ] Pruebas de conexión exitosas

---

## 🆘 Soporte y Recursos

### Documentación Oficial
- [Supabase Docs](https://supabase.com/docs)
- [Render Docs - Laravel](https://render.com/docs/deploy-laravel)
- [Laravel + PostgreSQL](https://laravel.com/docs/database#postgresql)

### Contactar Soporte
- **Supabase**: support@supabase.com o Discord
- **Render**: support@render.com o Dashboard → Support

---

## 🎯 Comandos Rápidos

```bash
# Local testing con variables de Supabase
export DB_HOST=tu-project.supabase.co
export DB_PORT=6543
export DB_PASSWORD=tu-password
php artisan migrate:status

# Ver conexión actual
php artisan db:show

# Limpiar caché después de cambios
php artisan config:clear
php artisan cache:clear

# Production deploy
git push origin main
```

---

**Última actualización**: 2025-04-24  
**Versión**: 1.0
