# 🔧 PASOS PARA SOLUCIONAR: "Database connection [postgres] not configured"

## ⚠️ PROBLEMA
El error dice que busca "postgres" pero Laravel usa "pgsql". Necesitas verificar y corregir las variables.

## ✅ PASOS A SEGUIR (UNO POR UNO)

### PASO 1: Verificar DATABASE_URL

1. **Ve a tu servicio "Diario_Nanysh-1" en Render**
2. **Haz clic en "Environment"** (menú lateral izquierdo)
3. **Busca la variable `DATABASE_URL`**
4. **Haz clic en el ícono del ojo 👁️** para ver el valor
5. **Verifica que tenga este formato:**
   ```
   postgresql://usuario:password@host:5432/database
   ```

**Si NO existe `DATABASE_URL` o está vacía:**
- Haz clic en "+ New" o "Add Environment Variable"
- **Key:** `DATABASE_URL`
- **Value:** Ve al PASO 2 para obtenerla

### PASO 2: Obtener la Internal Database URL

1. **Ve a tu base de datos "diario"** (la que está "Available")
2. **Haz clic en la pestaña "Info"** (o "Connections")
3. **Busca "Internal Database URL"**
4. **Copia la URL completa** (algo como):
   ```
   postgresql://diario_user:password123@dpg-xxxxx-a.oregon-postgres.render.com:5432/diario_xxxxx
   ```
5. **Pégala como valor de `DATABASE_URL`** en tu web service

### PASO 3: Verificar DB_CONNECTION

1. **En "Environment" de tu web service**
2. **Busca `DB_CONNECTION`**
3. **Verifica que el valor sea exactamente:** `pgsql`
   - ❌ NO debe ser: `postgres`
   - ✅ DEBE ser: `pgsql`

**Si no existe o está mal:**
- Edita la variable `DB_CONNECTION`
- Cambia el valor a: `pgsql`
- Guarda

### PASO 4: Eliminar Variables Duplicadas (IMPORTANTE)

Si tienes **AMBAS** `DATABASE_URL` y las variables individuales (`DB_HOST`, `DB_PORT`, etc.), puede haber conflicto.

**Opción A: Usar solo DATABASE_URL (RECOMENDADO) ⭐**
- Mantén `DATABASE_URL`
- **ELIMINA** estas variables si existen:
  - `DB_HOST`
  - `DB_PORT`
  - `DB_DATABASE`
  - `DB_USERNAME`
  - `DB_PASSWORD`
- **MANTÉN** solo: `DB_CONNECTION=pgsql`

**Opción B: Usar solo variables individuales**
- **ELIMINA** `DATABASE_URL`
- **MANTÉN** todas las variables `DB_*` con valores correctos

### PASO 5: Verificar APP_KEY

1. **Busca `APP_KEY` en las variables**
2. **Verifica que tenga un valor** (no esté vacía)
3. **Si está vacía o no existe:**
   - Genera una localmente:
     ```bash
     php artisan key:generate --show
     ```
   - Copia el resultado
   - Agrega/edita `APP_KEY` con ese valor

### PASO 6: Verificar Otras Variables Necesarias

Asegúrate de tener estas variables:

- ✅ `APP_ENV=production`
- ✅ `APP_DEBUG=false`
- ✅ `APP_URL=https://diario-nanysh-1.onrender.com` (o tu URL real)
- ✅ `APP_KEY=` (con un valor generado)
- ✅ `DATABASE_URL=` (con la Internal Database URL)
- ✅ `DB_CONNECTION=pgsql`

### PASO 7: Guardar y Redesplegar

1. **Guarda todos los cambios** en las variables de entorno
2. **Ve a la pestaña "Events" o "Logs"**
3. **Haz clic en "Manual Deploy"** → **"Deploy latest commit"**
4. **O espera** a que Render redesplegue automáticamente

### PASO 8: Verificar los Logs

1. **Después del deploy, ve a "Logs"**
2. **Busca estos mensajes:**
   - ✅ `"Running migrations..."` = Funciona
   - ✅ `"Migration completed"` = ¡Éxito!
   - ❌ `"Database connection [postgres] not configured"` = Aún hay problema

## 🎯 RESUMEN RÁPIDO

1. ✅ Verifica que `DATABASE_URL` tenga la Internal Database URL completa
2. ✅ Verifica que `DB_CONNECTION=pgsql` (no "postgres")
3. ✅ Elimina variables duplicadas o conflictivas
4. ✅ Verifica que `APP_KEY` tenga un valor
5. ✅ Guarda y redesplega

## ⚠️ ERRORES COMUNES

1. **Usar "postgres" en lugar de "pgsql"**
   - ❌ `DB_CONNECTION=postgres`
   - ✅ `DB_CONNECTION=pgsql`

2. **Usar la URL externa en lugar de la interna**
   - ❌ External Database URL
   - ✅ Internal Database URL

3. **Tener ambas DATABASE_URL y variables DB_* individuales**
   - Elige UNA opción, no ambas

4. **APP_KEY vacía o mal formada**
   - Debe empezar con `base64:`

## 🆘 Si Sigue Sin Funcionar

1. **Elimina TODAS las variables de base de datos**
2. **Agrega SOLO estas dos:**
   - `DATABASE_URL=` (Internal Database URL completa)
   - `DB_CONNECTION=pgsql`
3. **Redesplega**

