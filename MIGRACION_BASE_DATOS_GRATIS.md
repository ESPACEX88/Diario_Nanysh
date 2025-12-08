# 🆓 Guía: Migrar a Base de Datos Gratuita (Alternativas a Render)

## ⚠️ URGENTE: Tienes 12 días para migrar tus datos

Tu base de datos PostgreSQL en Render ha expirado y será eliminada en 12 días. Aquí tienes las mejores alternativas **100% GRATUITAS**:

---

## 🏆 Mejores Opciones Gratuitas (Recomendadas)

### 1. **Neon** ⭐ LA MEJOR OPCIÓN
- ✅ **PostgreSQL gratuito** con 3GB de almacenamiento
- ✅ **Sin expiración** (permanente mientras uses)
- ✅ **Serverless** (se duerme después de inactividad, pero se despierta rápido)
- ✅ **Backups automáticos**
- ✅ **Muy fácil de configurar**

**Pasos para migrar a Neon:**
1. Ve a https://neon.tech y crea una cuenta gratuita
2. Crea un nuevo proyecto PostgreSQL
3. Copia la cadena de conexión que te dan
4. Actualiza tu variable `DATABASE_URL` en Render con la nueva URL
5. Exporta tus datos de Render e impórtalos a Neon (ver sección de migración abajo)

---

### 2. **Supabase** ⭐ EXCELENTE OPCIÓN
- ✅ **PostgreSQL gratuito** con 500MB de almacenamiento
- ✅ **Sin expiración**
- ✅ **Panel de administración visual**
- ✅ **APIs REST automáticas**
- ✅ **Autenticación incluida**

**Pasos para migrar a Supabase:**
1. Ve a https://supabase.com y crea una cuenta gratuita
2. Crea un nuevo proyecto
3. Ve a Settings → Database → Connection string
4. Copia la cadena de conexión
5. Actualiza tu variable `DATABASE_URL` en Render
6. Exporta e importa tus datos

---

### 3. **Railway** ⭐ BUENA OPCIÓN
- ✅ **PostgreSQL gratuito** con $5 de crédito mensual
- ✅ **Sin expiración** (mientras tengas crédito)
- ✅ **Muy fácil de usar**
- ⚠️ Puede consumir crédito si hay mucho tráfico

**Pasos para migrar a Railway:**
1. Ve a https://railway.app y crea una cuenta
2. Crea un nuevo proyecto → Add PostgreSQL
3. Copia la cadena de conexión
4. Actualiza tu variable `DATABASE_URL` en Render
5. Exporta e importa tus datos

---

### 4. **ElephantSQL** (Solo para proyectos pequeños)
- ✅ **PostgreSQL gratuito** con 20MB de almacenamiento
- ✅ **Sin expiración**
- ⚠️ **Muy limitado** (solo 20MB)

**Pasos:**
1. Ve a https://www.elephantsql.com y crea cuenta
2. Crea una instancia "Tiny Turtle" (gratis)
3. Copia la cadena de conexión
4. Actualiza tu variable `DATABASE_URL`

---

## 📦 Cómo Migrar tus Datos (PASO A PASO)

### Opción A: Usando pg_dump (RECOMENDADO)

#### 1. Exportar datos desde Render (antes de que expire):

```bash
# Desde tu computadora local, ejecuta:
pg_dump "postgresql://diario_fhd4_user:z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY@dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com:5432/diario_fhd4" > backup.sql
```

**Si no tienes pg_dump instalado:**
- **Windows**: Descarga desde https://www.postgresql.org/download/windows/
- O usa DBeaver (ver opción B)

#### 2. Importar a la nueva base de datos:

```bash
# Para Neon/Supabase/Railway (reemplaza con tu nueva URL):
psql "TU_NUEVA_DATABASE_URL" < backup.sql
```

---

### Opción B: Usando DBeaver (Más Fácil - Visual)

1. **Conecta a Render** (mientras aún funciona):
   - Abre DBeaver
   - Nueva conexión → PostgreSQL
   - Host: `dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com`
   - Port: `5432`
   - Database: `diario_fhd4`
   - Username: `diario_fhd4_user`
   - Password: `z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY`

2. **Exportar datos**:
   - Click derecho en la base de datos → Tools → Backup Database
   - Selecciona todas las tablas
   - Guarda el archivo como `backup.sql`

3. **Conecta a tu nueva base de datos** (Neon/Supabase/etc):
   - Nueva conexión con los datos de tu nuevo servicio

4. **Importar datos**:
   - Click derecho en la nueva base de datos → Tools → Restore Database
   - Selecciona el archivo `backup.sql`
   - Ejecuta

---

### Opción C: Usando Laravel Migrations + Seeders

Si prefieres recrear la estructura y solo migrar datos importantes:

```bash
# 1. En tu nueva base de datos, ejecuta migraciones:
php artisan migrate

# 2. Exporta datos específicos desde Render usando Tinker:
php artisan tinker
# Luego exporta los datos que necesites a JSON o CSV
```

---

## 🔧 Actualizar Configuración en Render

Una vez que tengas tu nueva base de datos:

1. Ve a tu servicio web en Render
2. Ve a **Environment** → **Environment Variables**
3. Actualiza `DATABASE_URL` con la nueva cadena de conexión
4. O actualiza estas variables individuales:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`

5. **Reinicia el servicio** para que tome los nuevos valores

---

## 🎯 Recomendación Final

**Para tu caso, te recomiendo NEON porque:**
- ✅ 3GB es mucho más que suficiente para un diario personal
- ✅ No expira nunca
- ✅ Es gratis permanentemente
- ✅ Muy fácil de configurar
- ✅ Backups automáticos

**Pasos rápidos con Neon:**
1. Ve a https://neon.tech → Sign up (gratis)
2. Create project → PostgreSQL
3. Copia la Connection String
4. Actualiza `DATABASE_URL` en Render
5. Migra tus datos usando DBeaver o pg_dump

---

## ⚠️ IMPORTANTE: Haz esto HOY

1. **Exporta tus datos AHORA** (antes de que Render los elimine)
2. **Crea cuenta en Neon o Supabase** (5 minutos)
3. **Importa tus datos** (10 minutos)
4. **Actualiza la configuración en Render** (2 minutos)

**Total: ~20 minutos y tendrás tu base de datos gratis para siempre**

---

## 📞 ¿Necesitas Ayuda?

Si tienes problemas con la migración:
1. Primero exporta tus datos (lo más importante)
2. Luego podemos configurar la nueva base de datos juntos

---

## 🔄 Alternativa: SQLite (Solo para desarrollo local)

Si tu aplicación es solo para uso personal y no necesitas acceso remoto, puedes usar SQLite:

1. Cambia `DB_CONNECTION=sqlite` en tu `.env`
2. Laravel creará automáticamente `database/database.sqlite`
3. Ejecuta `php artisan migrate`

**Ventajas:**
- ✅ 100% gratis
- ✅ No necesita servidor
- ✅ Muy rápido

**Desventajas:**
- ❌ No funciona bien con múltiples usuarios simultáneos
- ❌ No es ideal para producción en la nube

---

## 📝 Checklist de Migración

- [ ] Crear cuenta en Neon/Supabase/Railway
- [ ] Crear nueva base de datos PostgreSQL
- [ ] Exportar datos desde Render (usando DBeaver o pg_dump)
- [ ] Importar datos a la nueva base de datos
- [ ] Actualizar `DATABASE_URL` en Render
- [ ] Reiniciar servicio en Render
- [ ] Verificar que la aplicación funciona correctamente
- [ ] Probar crear/editar/eliminar datos

---

**¡No esperes! Haz la migración hoy para no perder tus datos.**

