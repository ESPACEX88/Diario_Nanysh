# 🔧 Configurar DBeaver para Render PostgreSQL

## 📋 Información de tu Base de Datos

De tu URL:
```
postgresql://diario_fhd4_user:z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY@dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com/diario_fhd4
```

Los parámetros son:
- **Host**: `dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com`
- **Port**: `5432` (puerto por defecto de PostgreSQL)
- **Database**: `diario_fhd4`
- **Username**: `diario_fhd4_user`
- **Password**: `z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY`

## 🚀 Pasos para Configurar DBeaver

### 1. Abrir DBeaver
- Abre DBeaver en tu computadora

### 2. Crear Nueva Conexión
1. Click en el botón **"New Database Connection"** (icono de enchufe con +)
   - O ve a: `Database` → `New Database Connection`

### 3. Seleccionar PostgreSQL
1. En la lista de bases de datos, busca y selecciona **PostgreSQL**
2. Click en **"Next"**

### 4. Configurar Parámetros de Conexión

En la pestaña **"Main"**, ingresa:

#### Parámetros Básicos:
- **Host**: `dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com`
- **Port**: `5432`
- **Database**: `diario_fhd4`
- **Username**: `diario_fhd4_user`
- **Password**: `z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY`
  - ✅ Marca la casilla **"Save password"** si quieres que la recuerde

#### Configuración Avanzada (Pestaña "SSL"):
- **SSL Mode**: Selecciona **"require"** o **"prefer"**
  - Render requiere SSL para conexiones externas

### 5. Probar Conexión
1. Click en el botón **"Test Connection"** (abajo a la izquierda)
2. Si es la primera vez, DBeaver te pedirá descargar el driver de PostgreSQL
   - Click en **"Download"** y espera a que se descargue
3. Deberías ver: ✅ **"Connected"** o **"Success"**

### 6. Guardar Conexión
1. Si la conexión funciona, click en **"Finish"**
2. La conexión aparecerá en el panel izquierdo de DBeaver

## 🔍 Verificar Conexión

Una vez conectado:
1. Expande la conexión en el panel izquierdo
2. Expande **"Databases"** → **"diario_fhd4"** → **"Schemas"** → **"public"** → **"Tables"**
3. Deberías ver todas tus tablas:
   - `users`
   - `diary_entries`
   - `todos`
   - `events`
   - `wishlist_items`
   - `achievements`
   - `day_counters`
   - `dreams`
   - `media_items`
   - `cycle_trackings`
   - `favorite_meals`
   - `motivational_quotes`
   - `pets`
   - etc.

## 📝 Ejemplos de Consultas

Una vez conectado, puedes ejecutar SQL:

```sql
-- Ver todas las tablas
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public';

-- Ver todos los usuarios
SELECT * FROM users;

-- Ver todas las entradas del diario
SELECT * FROM diary_entries ORDER BY created_at DESC;

-- Ver todas las tareas
SELECT * FROM todos;

-- Ver todas las frases motivacionales
SELECT * FROM motivational_quotes;

-- Ver todos los logros
SELECT * FROM achievements;

-- Ver la mascota
SELECT * FROM pets;
```

## ⚠️ Solución de Problemas

### Error: "Connection refused"
- Verifica que el **Host** y **Port** sean correctos
- Asegúrate de que la base de datos esté activa en Render

### Error: "SSL required"
- Ve a la pestaña **"SSL"** en la configuración
- Cambia **SSL Mode** a **"require"**

### Error: "Authentication failed"
- Verifica que el **Username** y **Password** sean correctos
- La contraseña puede tener caracteres especiales, cópiala exactamente

### Error: "Database does not exist"
- Verifica que el nombre de la **Database** sea exactamente: `diario_fhd4`

### No puedo ver las tablas
- Asegúrate de expandir: `Databases` → `diario_fhd4` → `Schemas` → `public` → `Tables`
- Click derecho en la conexión → **"Refresh"**

## 🎯 Consejos

1. **Guarda la contraseña**: Marca "Save password" para no tener que ingresarla cada vez
2. **Usa SSL**: Siempre usa SSL para conexiones externas
3. **Refresh**: Si no ves cambios, click derecho → "Refresh"
4. **Backup**: Antes de hacer cambios grandes, exporta los datos

## 📊 Ver Datos en DBeaver

1. **Ver datos de una tabla**:
   - Expande la tabla en el panel izquierdo
   - Click derecho → **"View Data"** → **"All Rows"**

2. **Editar datos**:
   - Abre "View Data"
   - Modifica los valores directamente en la tabla
   - Click en el botón **"Save"** (disquete) para guardar

3. **Ejecutar SQL**:
   - Click en el botón **"SQL Editor"** (icono de documento con SQL)
   - Escribe tu consulta
   - Click en **"Execute SQL"** (▶️) o presiona `Ctrl+Enter`


