# 🔄 Guía: Migrar Datos de Render a Neon

## ✅ Estado Actual
- ✅ Ya tienes tu proyecto en Neon creado
- ✅ Ya conectaste Render con Neon
- ⏳ Ahora necesitas migrar los datos de la base antigua

---

## 🎯 Método 1: Usando DBeaver (MÁS FÁCIL - Recomendado)

### Paso 1: Conectar a la Base de Datos Antigua de Render

1. **Abre DBeaver** (si no lo tienes, descárgalo de https://dbeaver.io/download/)

2. **Crea nueva conexión a Render**:
   - Click en "New Database Connection" (icono de enchufe con +)
   - Selecciona **PostgreSQL**
   - Click en **Next**

3. **Configura la conexión a Render**:
   - **Host**: `dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com`
   - **Port**: `5432`
   - **Database**: `diario_fhd4`
   - **Username**: `diario_fhd4_user`
   - **Password**: `z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY`
   - En la pestaña **SSL**: Selecciona **"require"** o **"prefer"**
   - ✅ Marca **"Save password"**
   - Click en **"Test Connection"** → Debería decir "Connected"
   - Click en **"Finish"**

4. **Nombra la conexión**: "Render (Antigua)" para identificarla

---

### Paso 2: Conectar a tu Base de Datos de Neon

1. **Obtén la cadena de conexión de Neon**:
   - Ve a tu proyecto en https://console.neon.tech
   - Click en **"Connection Details"** o **"Connection String"**
   - Copia la cadena que se ve así:
     ```
     postgresql://usuario:password@host.neon.tech/database?sslmode=require
     ```

2. **Extrae los datos de la cadena**:
   - Si la cadena es: `postgresql://user:pass@host.neon.tech/dbname?sslmode=require`
   - **Host**: `host.neon.tech` (sin el `postgresql://` ni el puerto)
   - **Port**: `5432` (por defecto)
   - **Database**: `dbname` (el nombre después de la última `/`)
   - **Username**: `user` (antes del `:`)
   - **Password**: `pass` (entre `:` y `@`)

3. **Crea nueva conexión en DBeaver a Neon**:
   - Nueva conexión → PostgreSQL
   - Ingresa los datos extraídos arriba
   - SSL Mode: **"require"**
   - Click en **"Test Connection"** → Debería conectar
   - Click en **"Finish"**

4. **Nombra la conexión**: "Neon (Nueva)"

---

### Paso 3: Exportar Datos de Render

1. **En DBeaver, conecta a "Render (Antigua)"**

2. **Exportar la estructura y datos**:
   - Click derecho en la base de datos **"diario_fhd4"**
   - Ve a **Tools** → **Backup Database** (o **Export Data**)
   
3. **Configurar la exportación**:
   - **Format**: Selecciona **"SQL"** o **"Plain SQL"**
   - **Objects**: Selecciona **"All objects"** o marca todas las tablas
   - **Options**:
     - ✅ Marca **"Include CREATE statements"**
     - ✅ Marca **"Include DROP statements"** (opcional, para limpiar primero)
     - ✅ Marca **"Include data"** (MUY IMPORTANTE)
     - ✅ Marca **"Include constraints"**
     - ✅ Marca **"Include indexes"**
   - **Output**: Selecciona dónde guardar el archivo (ej: `C:\backup_render.sql`)
   - Click en **"Start"** o **"Export"**

4. **Espera a que termine** (puede tardar unos minutos dependiendo del tamaño)

---

### Paso 4: Importar Datos a Neon

1. **En DBeaver, conecta a "Neon (Nueva)"**

2. **Verificar que Neon esté vacío** (o ejecutar migraciones primero):
   - Expande la base de datos → Schemas → public → Tables
   - Si ya tienes tablas (de las migraciones), está bien
   - Si no, primero ejecuta las migraciones desde Render Shell:
     ```bash
     php artisan migrate
     ```

3. **Importar los datos**:
   - Click derecho en la base de datos de Neon
   - Ve a **Tools** → **Restore Database** (o **Execute SQL Script**)
   - Selecciona el archivo `backup_render.sql` que guardaste
   - Click en **"Start"** o **"Execute"**

4. **Si hay errores de tablas duplicadas**:
   - Abre el archivo `backup_render.sql` en un editor de texto
   - Busca y elimina las líneas que dicen `CREATE TABLE` (solo mantén los `INSERT`)
   - O mejor aún, usa el script PHP que creé abajo (Método 2)

---

## 🎯 Método 2: Usando Script PHP (AUTOMÁTICO)

He creado un script PHP que migra los datos automáticamente. Es más seguro porque:
- ✅ Migra tabla por tabla
- ✅ Maneja errores automáticamente
- ✅ Muestra progreso
- ✅ No duplica datos si ya existen

**Ver el archivo**: `migrate_to_neon.php` (creado abajo)

---

## 🎯 Método 3: Usando pg_dump (Línea de Comandos)

Si tienes PostgreSQL instalado en tu computadora:

### Paso 1: Exportar desde Render

```powershell
# En PowerShell (Windows)
pg_dump "postgresql://diario_fhd4_user:z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY@dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com:5432/diario_fhd4" -F c -f backup_render.dump
```

O en formato SQL plano:
```powershell
pg_dump "postgresql://diario_fhd4_user:z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY@dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com:5432/diario_fhd4" > backup_render.sql
```

### Paso 2: Importar a Neon

```powershell
# Reemplaza TU_DATABASE_URL_NEON con tu cadena de conexión de Neon
psql "TU_DATABASE_URL_NEON" < backup_render.sql
```

O si usaste formato custom:
```powershell
pg_restore -d "TU_DATABASE_URL_NEON" backup_render.dump
```

**Nota**: Si no tienes `pg_dump` instalado, descárgalo de:
https://www.postgresql.org/download/windows/

---

## ✅ Verificar que la Migración Funcionó

### Desde DBeaver:

1. **Conecta a Neon**
2. **Verifica las tablas**:
   - Expande: Database → Schemas → public → Tables
   - Deberías ver todas tus tablas: `users`, `diary_entries`, `todos`, etc.

3. **Verifica los datos**:
   - Click derecho en una tabla (ej: `users`)
   - **View Data** → Deberías ver tus usuarios
   - Repite con otras tablas importantes

### Desde Render Shell:

```bash
# Conecta a tu servicio en Render y abre Shell
php artisan tinker

# Verifica que los datos estén ahí:
\App\Models\User::count();
\App\Models\DiaryEntry::count();
\App\Models\Todo::count();
```

---

## 🔧 Solución de Problemas

### Error: "Table already exists"
**Solución**: El script de backup incluye `CREATE TABLE`. Tienes dos opciones:
1. Eliminar las líneas `CREATE TABLE` del archivo SQL
2. Usar el script PHP (Método 2) que maneja esto automáticamente

### Error: "Connection timeout"
**Solución**: 
- Verifica que la base de datos antigua de Render aún esté activa
- Verifica que la cadena de conexión de Neon sea correcta
- Asegúrate de usar SSL mode "require"

### Error: "Foreign key constraint"
**Solución**: 
- Importa primero las tablas sin foreign keys (ej: `users`)
- Luego importa las que dependen de ellas
- O desactiva temporalmente las foreign keys:
  ```sql
  SET session_replication_role = 'replica';
  -- Importa datos aquí
  SET session_replication_role = 'origin';
  ```

### Los datos no aparecen en la aplicación
**Solución**:
1. Verifica que `DATABASE_URL` en Render apunte a Neon
2. Reinicia el servicio en Render
3. Limpia el cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

---

## 📝 Checklist Final

- [ ] Conectado a base de datos antigua de Render en DBeaver
- [ ] Conectado a base de datos nueva de Neon en DBeaver
- [ ] Exportados todos los datos de Render (backup.sql)
- [ ] Importados todos los datos a Neon
- [ ] Verificados los datos en Neon (contar registros)
- [ ] Verificada la aplicación en Render (que funcione correctamente)
- [ ] Probado crear/editar/eliminar datos desde la app

---

## 🎉 ¡Listo!

Una vez completada la migración:
- ✅ Tus datos estarán seguros en Neon
- ✅ Tu aplicación seguirá funcionando igual
- ✅ No perderás nada cuando Render elimine la base antigua

**¡No olvides verificar que todo funcione antes de que Render elimine la base antigua!**

