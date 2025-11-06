# 🚀 Guía de Despliegue - Subir tu Diario a Internet

Esta guía te ayudará a subir tu aplicación Laravel para que tu novia pueda acceder desde cualquier lugar, dispositivo o red.

## 🏆 Opción 1: Railway (RECOMENDADO - Más Fácil)

### ⚠️ IMPORTANTE: Plan de Railway
- **$5 de crédito GRATIS** cada mes
- Después de $5, cobra por uso (muy barato: ~$0.000463/hora)
- Para una app pequeña como esta, $5 puede durar todo el mes o más
- **Si quieres 100% gratis**, ve a la Opción 2 (Render) o 3 (Fly.io)

### ✅ Ventajas
- Deploy automático desde GitHub
- Base de datos PostgreSQL incluida
- SSL/HTTPS automático
- Dominio gratuito (o puedes usar uno propio)
- Muy fácil de usar

### 📝 Pasos para Desplegar en Railway

#### 1. Preparar el Código

1. **Crear un repositorio en GitHub** (si no lo tienes)
   - Ve a: https://github.com
   - Crea un nuevo repositorio (ej: `diario-nahysh`)
   - **NO subas el archivo `.env`** (está en `.gitignore`)

2. **Subir tu código a GitHub**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/TU-USUARIO/diario-nahysh.git
   git push -u origin main
   ```

#### 2. Crear Cuenta en Railway

1. Ve a: https://railway.app
2. Haz clic en "Start a New Project"
3. Inicia sesión con GitHub

#### 3. Crear el Proyecto

1. Haz clic en "New Project"
2. Selecciona "Deploy from GitHub repo"
3. Elige tu repositorio `diario-nahysh`
4. Railway detectará automáticamente que es Laravel

#### 4. Agregar Base de Datos PostgreSQL

1. En tu proyecto, haz clic en "+ New"
2. Selecciona "Database"
3. Elige "PostgreSQL"
4. Railway creará automáticamente la base de datos

#### 5. Configurar Variables de Entorno

1. Haz clic en tu servicio Laravel
2. Ve a la pestaña "Variables"
3. Agrega estas variables:

```env
APP_NAME="Diario de Nahysh"
APP_ENV=production
APP_KEY=base64:TU-KEY-AQUI
APP_DEBUG=false
APP_URL=https://tu-app.railway.app

# Base de datos (Railway te da esto automáticamente)
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

# O usa la URL completa:
# DATABASE_URL=${{Postgres.DATABASE_URL}}

# Storage
FILESYSTEM_DISK=public

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

**Para generar APP_KEY:**
```bash
php artisan key:generate --show
```
Copia el resultado y pégalo en `APP_KEY`

#### 6. Configurar el Build

1. En tu servicio Laravel, ve a "Settings"
2. En "Build Command", agrega:
   ```bash
   composer install --no-dev --optimize-autoloader && php artisan migrate --force && npm ci && npm run build
   ```
3. En "Start Command", agrega:
   ```bash
   php artisan serve --host=0.0.0.0 --port=$PORT
   ```

#### 7. Ejecutar Migraciones

1. Ve a la pestaña "Deployments"
2. Haz clic en los tres puntos del último deployment
3. Selecciona "View Logs"
4. Verifica que las migraciones se ejecutaron correctamente

Si no se ejecutaron, puedes ejecutarlas manualmente:
1. Ve a "Settings" → "Deploy"
2. Agrega un "Deploy Hook" o usa la terminal:
   ```bash
   php artisan migrate --force
   ```

#### 8. Configurar Dominio

1. En tu servicio Laravel, ve a "Settings"
2. En "Domains", haz clic en "Generate Domain"
3. Railway te dará una URL como: `tu-app.up.railway.app`
4. Actualiza `APP_URL` en las variables de entorno con esta URL

#### 9. ¡Listo!

Tu aplicación estará disponible en: `https://tu-app.up.railway.app`

---

## 🌟 Opción 2: Render (100% GRATIS - Recomendado si quieres gratis)

### ✅ Ventajas
- **100% GRATIS** (sin tarjeta de crédito)
- Deploy automático desde GitHub
- SSL/HTTPS automático
- Base de datos PostgreSQL incluida (gratis)
- Sin límites de tiempo

### ⚠️ Desventajas
- El plan gratuito puede "dormir" después de 15 minutos de inactividad
- El primer acceso después de dormir puede tardar ~30 segundos en despertar
- Para una app personal, esto generalmente no es problema

### 📝 Pasos para Desplegar en Render

#### 1. Crear Cuenta
1. Ve a: https://render.com
2. Crea una cuenta (gratis, sin tarjeta de crédito)
3. Inicia sesión con GitHub

#### 2. Crear Base de Datos PostgreSQL
1. En el dashboard, haz clic en "New +"
2. Selecciona "PostgreSQL"
3. Configuración:
   - **Name:** `diario-nahysh-db`
   - **Database:** `diario_nahysh`
   - **User:** `diario_user`
   - **Plan:** Free
4. Haz clic en "Create Database"
5. **IMPORTANTE:** Copia las credenciales de conexión (las necesitarás después)

#### 3. Crear Web Service
1. Haz clic en "New +" → "Web Service"
2. Conecta tu repositorio de GitHub (`ESPACEX88/Diario_Nanysh`)
3. **Configuración IMPORTANTE:**
   - **Name:** `diario-nahysh`
   - **Environment:** `Docker` ⚠️ (Si no aparece "PHP", usa "Docker" - ya creé el Dockerfile)
   - **Region:** Elige la más cercana
   - **Branch:** `main`
   - **Root Directory:** (déjalo vacío)
   - **Plan:** Free

**Nota:** Si Render no muestra "PHP" en el menú, usa **"Docker"**. Ya creé un `Dockerfile` que Render usará automáticamente.

#### 4. Configurar Build y Start Commands
En la sección "Build & Deploy":

**Build Command:**
```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan migrate --force && php artisan storage:link
```

**Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

#### 5. Conectar la Base de Datos al Web Service

**IMPORTANTE:** Primero debes conectar la base de datos al servicio web.

1. **Ve a tu servicio web "Diario Nanysh-1"**
2. **Haz clic en "Environment"** (menú lateral)
3. **Haz clic en "Add Environment Variable"**

**Opción A: Usar Internal Database URL (MÁS FÁCIL) ⭐**

1. Ve a tu base de datos "diario"
2. En la pestaña "Info" o "Connections", copia la **Internal Database URL**
3. En tu web service, agrega:
   - **Key:** `DATABASE_URL`
   - **Value:** (pega la URL completa que copiaste)

**Opción B: Variables Individuales**

Si prefieres usar variables individuales, agrega:

- `DB_CONNECTION=pgsql`
- `DB_HOST=` (el host de tu base de datos)
- `DB_PORT=5432`
- `DB_DATABASE=` (nombre de tu base de datos)
- `DB_USERNAME=` (usuario de tu base de datos)
- `DB_PASSWORD=` (contraseña de tu base de datos)

#### 6. Configurar Otras Variables de Entorno

Agrega estas variables adicionales:

```env
APP_NAME="Diario de Nahysh"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:TU-KEY-AQUI
APP_URL=https://diario-nanysh-1.onrender.com

# Storage
FILESYSTEM_DISK=public

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

**Para generar APP_KEY:**
```bash
php artisan key:generate --show
```
Copia el resultado y pégalo en `APP_KEY`

**Para obtener credenciales de la base de datos:**
- Ve a tu base de datos PostgreSQL en Render
- En "Info" o "Connections", verás:
  - **Internal Database URL:** (úsala directamente como `DATABASE_URL`)
  - O las credenciales individuales si prefieres usarlas

#### 6. Desplegar
1. Haz clic en "Create Web Service"
2. Render comenzará a construir y desplegar tu aplicación
3. Esto puede tardar 5-10 minutos la primera vez
4. Una vez terminado, tu app estará en: `https://diario-nahysh.onrender.com`

**Nota:** El plan gratuito puede "dormir" después de 15 minutos de inactividad, pero se despierta automáticamente cuando alguien accede (tarda ~30 segundos).

---

## 🚁 Opción 3: Fly.io (Gratis con Límites)

### ✅ Ventajas
- **Plan gratuito generoso**
- Muy rápido
- Global edge network
- PostgreSQL incluido

### 📝 Pasos para Desplegar en Fly.io

1. **Instalar Fly CLI:**
   ```bash
   # Windows (PowerShell)
   iwr https://fly.io/install.ps1 -useb | iex
   ```

2. **Iniciar sesión:**
   ```bash
   fly auth login
   ```

3. **Inicializar proyecto:**
   ```bash
   fly launch
   ```

4. **Configurar base de datos:**
   ```bash
   fly postgres create
   fly postgres attach --app tu-app
   ```

5. **Desplegar:**
   ```bash
   fly deploy
   ```

---

## 📋 Checklist Antes de Desplegar

### ✅ Preparar el Código

- [ ] El código está en GitHub
- [ ] `.env` NO está en el repositorio (verifica `.gitignore`)
- [ ] `APP_DEBUG=false` en producción
- [ ] `APP_ENV=production` en producción
- [ ] `APP_KEY` generado

### ✅ Base de Datos

- [ ] Base de datos PostgreSQL creada
- [ ] Credenciales guardadas de forma segura
- [ ] Migraciones listas para ejecutar

### ✅ Assets

- [ ] `npm run build` ejecutado localmente (verificar que funciona)
- [ ] Archivos en `public/build/` compilados

### ✅ Storage

- [ ] `storage/app/public` configurado
- [ ] `php artisan storage:link` ejecutado (o configurado en el servidor)

---

## 🔧 Configuración de Producción

### Variables de Entorno Importantes

```env
# Aplicación
APP_NAME="Diario de Nahysh"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos
DB_CONNECTION=pgsql
DB_HOST=tu-host
DB_PORT=5432
DB_DATABASE=tu-database
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password

# Storage
FILESYSTEM_DISK=public

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# Mail (opcional, para notificaciones)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@tu-dominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🎯 Recomendación Final

### 💰 Si quieres 100% GRATIS (sin tarjeta):
**Usa Render** ⭐
- 100% gratis, sin tarjeta de crédito
- Solo "duerme" después de 15 min de inactividad
- Perfecto para una app personal
- Se despierta automáticamente cuando alguien accede

### 🚀 Si no te importa pagar después de $5/mes:
**Usa Railway**
- Más fácil de configurar
- $5 crédito gratis/mes
- Nunca "duerme"
- Mejor experiencia de usuario

### ⚡ Si quieres algo técnico pero potente:
**Usa Fly.io**
- Plan gratuito generoso
- Muy rápido
- Más control

---

## 🆘 Solución de Problemas

### Error: "APP_KEY is not set"
```bash
php artisan key:generate --show
```
Copia el resultado y agrégalo como variable de entorno `APP_KEY`

### Error: "Database connection failed"
- Verifica las credenciales de la base de datos
- Asegúrate de que la base de datos esté creada
- Verifica que las variables de entorno estén correctas

### Error: "Storage link not found"
En el servidor, ejecuta:
```bash
php artisan storage:link
```

### Error: "Assets not loading"
- Verifica que `npm run build` se ejecutó correctamente
- Revisa que los archivos estén en `public/build/`
- Verifica que `APP_URL` esté configurado correctamente

### La aplicación está lenta
- Verifica que `APP_DEBUG=false`
- Habilita el cache: `php artisan config:cache`
- Habilita el cache de rutas: `php artisan route:cache`

---

## 📱 Acceso desde Móvil

Una vez desplegada, tu novia puede:
1. **Abrir en el navegador móvil:** `https://tu-app.up.railway.app`
2. **Agregar a la pantalla de inicio** (como una app)
   - En iOS: Compartir → Agregar a pantalla de inicio
   - En Android: Menú → Agregar a pantalla de inicio

---

## 🔒 Seguridad

- ✅ Nunca subas el archivo `.env` a GitHub
- ✅ Usa `APP_DEBUG=false` en producción
- ✅ Usa contraseñas seguras para la base de datos
- ✅ Habilita HTTPS (Railway lo hace automáticamente)
- ✅ Mantén Laravel actualizado

---

## 💡 Tips Adicionales

1. **Dominio personalizado:** Puedes conectar tu propio dominio en Railway
2. **Backups:** Railway hace backups automáticos de la base de datos
3. **Monitoreo:** Railway te muestra logs en tiempo real
4. **Escalado:** Si necesitas más recursos, puedes actualizar el plan

---

¡Listo! Tu aplicación estará disponible para tu novia desde cualquier lugar. 💕

