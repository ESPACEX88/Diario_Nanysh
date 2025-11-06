# 🔧 Solución: "Database connection [postgres] not configured"

## ❌ Error

```
Database connection [postgres] not configured.
```

## ✅ Solución

El problema es que las variables de entorno de la base de datos no están configuradas correctamente en Render.

### Pasos para Solucionar:

#### 1. Verificar Variables de Entorno en Render

1. **Ve a tu servicio web "Diario Nanysh-1"**
2. **Haz clic en "Environment"** (menú lateral)
3. **Verifica que tengas estas variables:**

**Opción A: Usar DATABASE_URL (RECOMENDADO) ⭐**

- **Key:** `DATABASE_URL`
- **Value:** La Internal Database URL completa de tu base de datos
  - Ejemplo: `postgresql://usuario:password@dpg-xxxxx-a.oregon-postgres.render.com:5432/diario_xxxxx`

**Opción B: Variables Individuales**

- `DB_CONNECTION=pgsql` ⚠️ **IMPORTANTE: debe ser "pgsql", no "postgres"**
- `DB_HOST=` (el host de tu base de datos)
- `DB_PORT=5432`
- `DB_DATABASE=` (nombre de tu base de datos)
- `DB_USERNAME=` (usuario)
- `DB_PASSWORD=` (contraseña)

#### 2. Obtener la Internal Database URL

1. **Ve a tu base de datos "diario"**
2. **En la pestaña "Info" o "Connections"**
3. **Copia la "Internal Database URL"** (no la externa)
4. **Pégala como valor de `DATABASE_URL` en tu web service**

#### 3. Verificar Otras Variables Necesarias

Asegúrate de tener también:

- `APP_KEY=` (generada con `php artisan key:generate --show`)
- `APP_URL=` (la URL de tu servicio)
- `APP_ENV=production`
- `APP_DEBUG=false`

#### 4. Redesplegar

1. **Guarda todas las variables**
2. **Ve a "Manual Deploy"** → **"Deploy latest commit"**
3. O espera a que Render redesplegue automáticamente

## 🎯 Método Más Fácil

**Usa solo `DATABASE_URL`:**

1. Ve a tu base de datos
2. Copia la **Internal Database URL**
3. En tu web service, agrega:
   - **Key:** `DATABASE_URL`
   - **Value:** (pega la URL completa)
4. **NO necesitas** las otras variables DB_* si usas DATABASE_URL

Laravel detectará automáticamente `DATABASE_URL` y la usará.

## ⚠️ Errores Comunes

1. **Usar "postgres" en lugar de "pgsql"**
   - ❌ `DB_CONNECTION=postgres`
   - ✅ `DB_CONNECTION=pgsql`

2. **Usar la URL externa en lugar de la interna**
   - ❌ External Database URL
   - ✅ Internal Database URL

3. **Faltan comillas en valores con caracteres especiales**
   - Si la contraseña tiene caracteres especiales, puede necesitar comillas

## 🔍 Verificar que Funciona

Después de redesplegar, revisa los logs:
1. Ve a tu web service
2. Haz clic en "Logs"
3. Busca:
   - ✅ "Migration completed" = Funciona
   - ❌ "Database connection not configured" = Aún falta configurar

