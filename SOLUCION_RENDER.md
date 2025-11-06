# 🔧 Solución: Error "composer: command not found" en Render

## ❌ Problema

Render está detectando tu aplicación como Node.js en lugar de PHP, por eso no encuentra `composer`.

## ✅ Solución

### Opción 1: Configurar Manualmente en Render (Recomendado)

1. **Ve a tu servicio en Render**
2. **Ve a "Settings"**
3. **En "Environment":**
   - Cambia de "Node" a **"PHP"**
4. **Guarda los cambios**
5. **Haz un nuevo deploy**

### Opción 2: Usar render.yaml (Automático)

Si tienes el archivo `render.yaml` en tu repositorio, Render lo detectará automáticamente.

**Verifica que tu `render.yaml` tenga:**
```yaml
services:
  - type: web
    name: diario-nahysh
    runtime: php  # ← Esto es importante
    plan: free
```

### Opción 3: Configuración Manual Paso a Paso

1. **Elimina el servicio actual** (si ya lo creaste)
2. **Crea un nuevo Web Service**
3. **Al crear, asegúrate de:**
   - **Environment:** Selecciona **"PHP"** (no Node.js)
   - **Build Command:** 
     ```bash
     composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan migrate --force && php artisan storage:link
     ```
   - **Start Command:**
     ```bash
     php artisan serve --host=0.0.0.0 --port=$PORT
     ```

## 📋 Checklist de Configuración

- [ ] Environment está configurado como **"PHP"** (no Node.js)
- [ ] Build Command incluye `composer install`
- [ ] Start Command usa `php artisan serve`
- [ ] Variables de entorno están configuradas
- [ ] Base de datos PostgreSQL está creada
- [ ] Credenciales de base de datos están en variables de entorno

## 🎯 Configuración Correcta en Render

### Build Command:
```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan migrate --force && php artisan storage:link
```

### Start Command:
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

### Variables de Entorno Mínimas:
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:TU-KEY-AQUI
APP_URL=https://tu-app.onrender.com
DB_CONNECTION=pgsql
DB_HOST=tu-host
DB_PORT=5432
DB_DATABASE=tu-database
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password
```

## 🆘 Si Sigue Sin Funcionar

1. **Verifica los logs de build** en Render
2. **Asegúrate de que el archivo `composer.json` esté en el repositorio**
3. **Verifica que el repositorio esté conectado correctamente**
4. **Intenta hacer un "Manual Deploy" desde el dashboard**

