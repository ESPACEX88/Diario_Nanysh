# 🔧 SOLUCIÓN: "connection to server at 127.0.0.1 failed"

## ❌ PROBLEMA
Laravel está intentando conectarse a `127.0.0.1` (localhost) en lugar de la base de datos de Render.

## ✅ CAUSA
Las variables de entorno no están siendo leídas correctamente o no están configuradas.

## 🎯 SOLUCIÓN PASO A PASO

### PASO 1: Verificar DATABASE_URL en Render

1. **Ve a tu servicio "Diario_Nanysh-1"**
2. **Haz clic en "Environment"**
3. **Busca `DATABASE_URL`**
4. **Haz clic en el ojo 👁️ para ver el valor**
5. **DEBE tener la Internal Database URL completa**, ejemplo:
   ```
   postgresql://usuario:password@dpg-xxxxx-a.oregon-postgres.render.com:5432/diario_xxxxx
   ```

**Si NO existe o está vacía:**
- Ve al PASO 2

### PASO 2: Obtener la Internal Database URL

1. **Ve a tu base de datos "diario"**
2. **Pestaña "Info" o "Connections"**
3. **Busca "Internal Database URL"**
4. **Copia la URL completa**
5. **En tu web service, agrega/edita:**
   - **Key:** `DATABASE_URL`
   - **Value:** (pega la URL completa)
   - **GUARDA**

### PASO 3: Verificar que NO tengas variables conflictivas

**ELIMINA estas variables si existen:**
- `DB_HOST` (puede estar apuntando a 127.0.0.1)
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

**MANTÉN SOLO:**
- `DATABASE_URL` (con la Internal Database URL completa)
- `DB_CONNECTION=pgsql`

### PASO 4: Verificar DB_CONNECTION

1. **Busca `DB_CONNECTION`**
2. **DEBE ser:** `pgsql` (no "postgres", no "mysql")
3. **Si no existe, agrégalo:**
   - **Key:** `DB_CONNECTION`
   - **Value:** `pgsql`

### PASO 5: Verificar APP_KEY

1. **Busca `APP_KEY`**
2. **DEBE tener un valor** (no vacía)
3. **Si está vacía, genera una:**
   ```bash
   php artisan key:generate --show
   ```
   Copia el resultado y actualiza `APP_KEY`

### PASO 6: Guardar y Redesplegar

1. **Guarda TODOS los cambios**
2. **Ve a "Manual Deploy"** → **"Deploy latest commit"**
3. **O espera** a que Render redesplegue automáticamente

### PASO 7: Verificar Logs

Después del deploy, en "Logs" busca:
- ✅ `"Running migrations..."` = Funciona
- ✅ `"Migration completed"` = ¡Éxito!
- ❌ `"connection to server at 127.0.0.1"` = Aún hay problema

## 🎯 CONFIGURACIÓN CORRECTA

**Variables MÍNIMAS necesarias:**

```
DATABASE_URL=postgresql://usuario:password@host:5432/database
DB_CONNECTION=pgsql
APP_KEY=base64:tu-key-generada
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-app.onrender.com
```

## ⚠️ IMPORTANTE

1. **Usa la Internal Database URL** (no la externa)
2. **Elimina variables `DB_HOST`, `DB_PORT`, etc.** si usas `DATABASE_URL`
3. **No uses ambas opciones a la vez** (puede causar conflictos)

## 🆘 Si Sigue Sin Funcionar

1. **Elimina TODAS las variables de base de datos**
2. **Agrega SOLO estas dos:**
   - `DATABASE_URL=` (Internal Database URL completa)
   - `DB_CONNECTION=pgsql`
3. **Guarda y redesplega**

