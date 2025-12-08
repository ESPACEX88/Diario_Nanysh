# 🚨 Exportar Datos SIN Acceso al Shell (Plan Gratuito)

## ❌ Problema: No Tienes Acceso al Shell

El plan gratuito de Render **NO incluye Shell**. Necesitamos otras formas de exportar tus datos.

---

## 🎯 SOLUCIÓN 1: Conectar Directamente desde tu Computadora (RECOMENDADO)

Aunque Render diga "expirada", **a veces la conexión aún funciona** por unos días. Vamos a intentar conectarnos directamente.

### Opción A: Usar DBeaver (MÁS FÁCIL)

1. **Abre DBeaver** (si no lo tienes: https://dbeaver.io/download/)

2. **Crea nueva conexión**:
   - Click en "New Database Connection" (icono de enchufe con +)
   - Selecciona **PostgreSQL**
   - Click en **Next**

3. **Configura la conexión**:
   - **Host**: `dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com`
   - **Port**: `5432`
   - **Database**: `diario_fhd4`
   - **Username**: `diario_fhd4_user`
   - **Password**: `z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY`
   - **SSL Mode**: Selecciona **"require"**
   - ✅ Marca **"Save password"**

4. **Click en "Test Connection"**
   - **Si conecta**: ¡PERFECTO! Puedes exportar (ver abajo)
   - **Si NO conecta**: La base está completamente inaccesible (ver Solución 2)

5. **Si conectó, exporta los datos**:
   - Click derecho en la base de datos **"diario_fhd4"**
   - Ve a **Tools** → **Backup Database**
   - Selecciona **"All objects"** o marca todas las tablas
   - **Options**:
     - ✅ Marca **"Include CREATE statements"**
     - ✅ Marca **"Include data"** (MUY IMPORTANTE)
     - ✅ Marca **"Include constraints"**
   - **Output**: Selecciona dónde guardar (ej: `C:\backup_render.sql`)
   - Click en **"Start"** o **"Export"**

6. **Espera a que termine** (puede tardar unos minutos)

7. **Importa a Neon**:
   - Conecta DBeaver a Neon (nueva conexión con los datos de Neon)
   - Click derecho en la base de datos de Neon
   - **Tools** → **Execute SQL Script**
   - Selecciona el archivo `backup_render.sql`
   - Click en **"Start"**

---

### Opción B: Usar pg_dump desde tu Computadora

**Primero, instala PostgreSQL en tu computadora**:
- Descarga desde: https://www.postgresql.org/download/windows/
- O instala solo las herramientas: https://www.enterprisedb.com/downloads/postgres-postgresql-downloads

**Luego, desde PowerShell**:

```powershell
# Intenta conectarte primero (para verificar que funciona)
psql "postgresql://diario_fhd4_user:z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY@dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com:5432/diario_fhd4"
```

**Si conecta**, ejecuta:

```powershell
# Exportar toda la base de datos
pg_dump "postgresql://diario_fhd4_user:z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY@dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com:5432/diario_fhd4" > C:\backup_render.sql
```

**Luego importa a Neon**:

```powershell
# Reemplaza con tu DATABASE_URL de Neon
psql "TU_DATABASE_URL_NEON" < C:\backup_render.sql
```

---

## 🎯 SOLUCIÓN 2: Crear Endpoint de Exportación en tu App

Si no puedes conectar directamente, podemos crear un endpoint en tu aplicación web que exporte los datos.

### Paso 1: Crear Ruta de Exportación

He creado un controlador que exporta los datos. Solo necesitas agregar la ruta.

### Paso 2: Acceder al Endpoint

Una vez desplegado, puedes acceder desde tu navegador:
```
https://tu-app.onrender.com/api/export-database
```

Esto descargará un archivo JSON con todos tus datos.

---

## 🎯 SOLUCIÓN 3: Usar Script PHP Local

He creado un script PHP que puedes ejecutar desde tu computadora local (Laragon) que intentará conectarse directamente a Render y exportar los datos.

**Ver archivo**: `export_from_render_local.php`

---

## 🎯 SOLUCIÓN 4: Contactar Soporte de Render

Si NADA de lo anterior funciona:

1. Ve a https://dashboard.render.com
2. Click en **"Contact Support"** (abajo a la izquierda)
3. Escribe:

```
Hola,

Mi base de datos gratuita "diario" (ID: dpg-d46flmqli9vc73fdn76g-a) ha expirado 
y será eliminada en 12 días. 

No tengo acceso al Shell (plan gratuito) y no puedo exportar mis datos desde 
el dashboard (función deshabilitada para planes gratuitos).

¿Es posible que me den acceso temporal al Shell o que me ayuden a exportar 
mis datos antes de que se eliminen permanentemente?

Gracias.
```

---

## ⚡ PLAN DE ACCIÓN INMEDIATO

### 1. PRIMERO: Intenta DBeaver (5 minutos)
   - Descarga DBeaver si no lo tienes
   - Intenta conectar con las credenciales de Render
   - **Si conecta**: Exporta inmediatamente
   - **Si NO conecta**: Sigue al paso 2

### 2. SEGUNDO: Intenta pg_dump (10 minutos)
   - Instala PostgreSQL en tu computadora
   - Intenta conectar con psql
   - **Si conecta**: Exporta con pg_dump
   - **Si NO conecta**: Sigue al paso 3

### 3. TERCERO: Contacta Soporte (HOY)
   - No esperes
   - Contacta soporte de Render ahora
   - Explica tu situación

### 4. CUARTO: Si NADA funciona
   - Acepta que perderás los datos antiguos
   - Tu app seguirá funcionando (migraciones están en el código)
   - Solo perderás datos de usuario

---

## 🔍 Verificar si la Base Aún Funciona

**La clave es intentar conectarte DIRECTAMENTE desde tu computadora.**

Aunque Render diga "expirada", **a veces la conexión aún funciona por unos días más**.

**NO asumas que está inaccesible hasta que lo pruebes.**

---

## 📋 Checklist

- [ ] Intenté conectar con DBeaver
- [ ] Intenté conectar con pg_dump/psql
- [ ] Si nada funciona, contacté soporte de Render
- [ ] Si logré exportar, importé los datos a Neon
- [ ] Verifiqué que los datos están en Neon

---

## 💡 IMPORTANTE

**Prueba DBeaver PRIMERO. Es la opción más fácil y la más probable de funcionar.**

**Aunque la base esté "expirada", la conexión puede seguir funcionando por unos días.**

**No te rindas sin intentarlo.**

