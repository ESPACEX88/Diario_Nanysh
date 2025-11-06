# 🔗 Conectar Base de Datos a Web Service en Render

## 📋 Pasos para Conectar la Base de Datos

### 1. Obtener Credenciales de la Base de Datos

1. **Ve a tu base de datos "diario" en Render**
2. **En la pestaña "Connections" o "Info"**, verás:
   - **Internal Database URL:** `postgresql://diario_user:password@dpg-xxxxx-a/diario_xxxxx`
   - O las credenciales individuales:
     - **Host:** `dpg-xxxxx-a.oregon-postgres.render.com` (o similar)
     - **Port:** `5432`
     - **Database:** `diario_xxxxx` (o el nombre que le diste)
     - **User:** `diario_user` (o el usuario que creaste)
     - **Password:** (haz clic en "Show" para verla)

### 2. Agregar Variables de Entorno al Web Service

1. **Ve a tu servicio web "Diario Nanysh-1"**
2. **Haz clic en "Environment"** (en el menú lateral izquierdo)
3. **Haz clic en "Add Environment Variable"**

### 3. Agregar las Variables de Base de Datos

Agrega estas variables una por una:

#### Opción A: Usar la URL Completa (Más Fácil)

**Variable 1:**
- **Key:** `DATABASE_URL`
- **Value:** Copia la **Internal Database URL** completa de tu base de datos
  - Ejemplo: `postgresql://diario_user:password@dpg-xxxxx-a/diario_xxxxx`

#### Opción B: Variables Individuales (Más Control)

**Variable 1:**
- **Key:** `DB_CONNECTION`
- **Value:** `pgsql`

**Variable 2:**
- **Key:** `DB_HOST`
- **Value:** El host de tu base de datos (ej: `dpg-xxxxx-a.oregon-postgres.render.com`)

**Variable 3:**
- **Key:** `DB_PORT`
- **Value:** `5432`

**Variable 4:**
- **Key:** `DB_DATABASE`
- **Value:** El nombre de tu base de datos (ej: `diario_xxxxx`)

**Variable 5:**
- **Key:** `DB_USERNAME`
- **Value:** El usuario (ej: `diario_user`)

**Variable 6:**
- **Key:** `DB_PASSWORD`
- **Value:** La contraseña (cópiala de la base de datos)

### 4. Agregar Otras Variables Necesarias

También necesitas estas variables:

**APP_KEY:**
- **Key:** `APP_KEY`
- **Value:** Genera una con: `php artisan key:generate --show` (localmente) o usa una key base64

**APP_URL:**
- **Key:** `APP_URL`
- **Value:** `https://diario-nanysh-1.onrender.com` (o la URL que Render te dio)

**APP_ENV:**
- **Key:** `APP_ENV`
- **Value:** `production`

**APP_DEBUG:**
- **Key:** `APP_DEBUG`
- **Value:** `false`

**Otras variables importantes:**
- `SESSION_DRIVER=database`
- `CACHE_DRIVER=database`
- `QUEUE_CONNECTION=database`
- `FILESYSTEM_DISK=public`

### 5. Guardar y Redesplegar

1. **Guarda todas las variables**
2. **Ve a "Manual Deploy"** → **"Deploy latest commit"**
3. O espera a que Render redesplegue automáticamente

## 🎯 Método Rápido: Usar Internal Database URL

La forma más fácil es usar la **Internal Database URL** que Render proporciona:

1. Ve a tu base de datos
2. Copia la **Internal Database URL**
3. En tu web service, agrega:
   - **Key:** `DATABASE_URL`
   - **Value:** (pega la URL completa)

Laravel detectará automáticamente esta variable y la usará.

## ✅ Verificar que Funciona

Después de redesplegar, verifica los logs:
1. Ve a tu web service
2. Haz clic en "Logs"
3. Busca mensajes como "Migration completed" o errores de conexión

Si ves errores de conexión, verifica que:
- Las credenciales sean correctas
- Estés usando la **Internal Database URL** (no la externa)
- El servicio web y la base de datos estén en la misma región

