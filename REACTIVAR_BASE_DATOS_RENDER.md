# 🔄 Cómo Reactivar la Base de Datos de Render (Está Pausada)

## ⚠️ Problema: Base de Datos Pausada

Las bases de datos gratuitas de Render se "duermen" después de 90 días de inactividad. Necesitas reactivarla para poder exportar tus datos.

---

## 🎯 Solución 1: Reactivar desde el Dashboard de Render

### Paso 1: Ir al Dashboard de Render

1. Ve a https://dashboard.render.com
2. Inicia sesión con tu cuenta
3. Busca tu base de datos **"diario"** en la lista

### Paso 2: Reactivar la Base de Datos

1. **Click en tu base de datos** "diario"
2. Verás un mensaje que dice algo como:
   - "Database is paused" 
   - "Free database expired"
   - O un botón "Resume" / "Restart"

3. **Opciones para reactivar**:
   - **Si hay botón "Resume"**: Click en él
   - **Si hay botón "Restart"**: Click en él
   - **Si dice "Upgrade"**: Necesitas otra solución (ver abajo)

### Paso 3: Esperar a que se Active

- Render puede tardar 1-3 minutos en reactivar la base de datos
- Verás el estado cambiar de "Paused" a "Active"
- Una vez activa, tendrás acceso temporal para exportar

---

## 🎯 Solución 2: Si Dice "Expired" y No Puedes Reactivar

Si Render dice que la base de datos expiró y no te deja reactivarla, tienes estas opciones:

### Opción A: Usar el Servicio Web para "Despertar" la Base

Si tu aplicación web en Render aún está funcionando, puedes intentar despertar la base de datos haciendo una petición:

1. **Ve a tu aplicación web en Render** (no la base de datos)
2. **Abre el Shell del servicio web**:
   - Click en tu servicio web "diario-nahysh"
   - Ve a la pestaña **"Shell"**
   - Ejecuta:
     ```bash
     php artisan tinker
     ```
   - Luego intenta:
     ```php
     \App\Models\User::count();
     ```
   - Esto puede "despertar" la base de datos

3. **O haz una petición HTTP a tu app**:
   - Abre en el navegador: `https://tu-app.onrender.com`
   - Esto puede activar la conexión y despertar la base

### Opción B: Contactar Soporte de Render

1. Ve a https://dashboard.render.com
2. Click en **"Contact Support"** (abajo a la izquierda)
3. Explica que necesitas acceso temporal para exportar tus datos
4. Pide que reactiven la base de datos por 24 horas para hacer backup

---

## 🎯 Solución 3: Exportar desde Render Shell (Si Tienes Acceso)

Si puedes acceder al Shell de tu servicio web en Render:

### Paso 1: Abrir Shell en Render

1. Ve a tu **servicio web** (no la base de datos)
2. Click en **"Shell"** en el menú lateral
3. Se abrirá una terminal

### Paso 2: Exportar Datos usando pg_dump

```bash
# Exportar toda la base de datos
pg_dump $DATABASE_URL > /tmp/backup.sql

# O si DATABASE_URL no funciona, usa las variables individuales:
pg_dump -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE -f /tmp/backup.sql

# Ver el archivo
cat /tmp/backup.sql
```

### Paso 3: Descargar el Backup

El problema es que Render Shell no te permite descargar archivos directamente. Entonces:

**Opción 1: Copiar el contenido**
- Usa `cat /tmp/backup.sql` y copia todo el contenido
- Pégalo en un archivo `.sql` en tu computadora

**Opción 2: Usar Laravel para exportar a JSON**
- Crear un comando artisan que exporte los datos
- Ver solución 4 abajo

---

## 🎯 Solución 4: Exportar usando Laravel (RECOMENDADO si la base está activa)

He creado un comando de Laravel que exporta todos los datos a archivos JSON. Esto es útil porque:

- ✅ Funciona desde Render Shell
- ✅ No necesita conexión externa
- ✅ Puedes descargar los JSON fácilmente
- ✅ Luego importas a Neon

**Ver el archivo**: `app/Console/Commands/ExportDatabase.php` (lo creo abajo)

### Uso:

```bash
# Desde Render Shell
php artisan db:export

# Esto creará archivos JSON en storage/app/exports/
# Luego puedes descargarlos o copiarlos
```

---

## 🎯 Solución 5: Si NADA Funciona - Usar Backup de Render

Render a veces guarda backups automáticos:

1. Ve a tu base de datos en Render
2. Busca la sección **"Backups"** o **"Snapshots"**
3. Si hay backups disponibles, puedes:
   - Descargarlos
   - O restaurarlos en una nueva base de datos temporal

---

## ⚡ Solución RÁPIDA: Migrar Solo la Estructura y Empezar de Cero

Si no puedes acceder a los datos y la base está completamente inaccesible:

1. **Las migraciones ya están en tu código** (en `database/migrations/`)
2. **Los seeders también** (para datos iniciales como logros, frases motivacionales)
3. **Solo perderías los datos de usuario** (entradas del diario, tareas, etc.)

**Pasos**:
1. Conecta Neon con Render (ya lo hiciste)
2. Ejecuta migraciones: `php artisan migrate`
3. Ejecuta seeders: `php artisan db:seed`
4. Tu app funcionará, pero sin los datos antiguos

**Esto es solo si NO puedes recuperar los datos de ninguna forma.**

---

## 🔍 Verificar Estado de la Base de Datos

Para saber exactamente qué está pasando:

1. **Ve al dashboard de Render**
2. **Click en tu base de datos "diario"**
3. **Revisa el estado**:
   - 🟢 **Active**: Está funcionando
   - 🟡 **Paused**: Está pausada (puedes reactivarla)
   - 🔴 **Expired**: Expiró (más difícil de reactivar)
   - ⚪ **Deleting**: Se está eliminando (URGENTE)

---

## 📞 Pasos Inmediatos (Hazlo AHORA)

1. ✅ **Ve a Render Dashboard** → Busca tu base de datos
2. ✅ **Intenta reactivar** (botón Resume/Restart)
3. ✅ **Si no funciona, contacta soporte** de Render
4. ✅ **Mientras tanto, usa el comando de exportación** que creé abajo

---

## 🎯 Plan de Acción Recomendado

### Si puedes reactivar la base:

1. **Reactiva la base de datos** (Solución 1)
2. **Inmediatamente exporta los datos** usando DBeaver o el script PHP
3. **Importa a Neon**
4. **Verifica que todo funcione**

### Si NO puedes reactivar:

1. **Contacta soporte de Render** (pueden darte acceso temporal)
2. **Mientras tanto, prepara Neon** (ya lo tienes listo)
3. **Usa el comando de exportación** si logras acceso
4. **Si nada funciona, acepta que perderás los datos antiguos** y empieza de cero en Neon

---

## ⚠️ IMPORTANTE

**Tienes 12 días antes de que Render elimine permanentemente la base de datos.**

**Haz esto HOY:**
1. Intenta reactivar
2. Si funciona, exporta INMEDIATAMENTE
3. Si no funciona, contacta soporte

**No esperes hasta el último día.**

