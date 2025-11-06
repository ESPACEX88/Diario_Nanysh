# 🆓 Guía de Bases de Datos Gratuitas para Producción

Este proyecto está configurado para usar **PostgreSQL**. Aquí tienes las mejores opciones gratuitas:

## 🏆 Opción 1: Supabase (RECOMENDADO)

### ✅ Ventajas
- **500MB GRATIS** (suficiente para empezar)
- Backups automáticos diarios
- Dashboard hermoso y fácil de usar
- API REST automática
- Autenticación incluida (opcional)
- SSL incluido
- Muy rápido y confiable

### 📝 Pasos para Configurar Supabase

1. **Crear cuenta en Supabase**
   - Ve a: https://supabase.com
   - Haz clic en "Start your project"
   - Inicia sesión con GitHub (recomendado)

2. **Crear un nuevo proyecto**
   - Haz clic en "New Project"
   - Nombre: `diario-personal`
   - Contraseña de base de datos: **Guárdala bien**
   - Región: Elige la más cercana (ej: `South America (São Paulo)`)
   - Plan: **Free**
   - Haz clic en "Create new project"

3. **Esperar a que se cree** (2-3 minutos)

4. **Obtener credenciales de conexión**
   - En el dashboard, ve a **Settings** → **Database**
   - Busca la sección **Connection string**
   - Selecciona **URI** o **Connection pooling**
   - Copia la cadena de conexión

5. **Configurar .env**

   Opción A: Usar la cadena de conexión completa:
   ```env
   DB_URL=postgresql://postgres:[TU-PASSWORD]@db.xxxxx.supabase.co:5432/postgres
   ```

   Opción B: Configurar manualmente:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=db.xxxxx.supabase.co
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=tu_contraseña_aquí
   DB_SSLMODE=require
   ```

6. **Ejecutar migraciones**
   ```bash
   php artisan migrate
   ```

### 🔒 Seguridad

Supabase usa SSL por defecto. Asegúrate de tener `DB_SSLMODE=require` en tu `.env`.

---

## 🚂 Opción 2: Railway

### ✅ Ventajas
- **$5 de crédito GRATIS** cada mes
- Deploy automático desde GitHub
- Muy fácil de usar
- PostgreSQL incluido
- Sin configuración complicada

### 📝 Pasos para Configurar Railway

1. **Crear cuenta en Railway**
   - Ve a: https://railway.app
   - Inicia sesión con GitHub

2. **Crear nuevo proyecto**
   - Haz clic en "New Project"
   - Selecciona "Empty Project"

3. **Agregar base de datos PostgreSQL**
   - Haz clic en "+ New"
   - Selecciona "Database"
   - Elige "PostgreSQL"
   - Railway creará automáticamente la base de datos

4. **Obtener credenciales**
   - Haz clic en la base de datos creada
   - Ve a la pestaña "Variables"
   - Railway te dará una variable `DATABASE_URL`

5. **Configurar .env**
   ```env
   DB_URL=postgresql://postgres:[PASSWORD]@[HOST]:[PORT]/railway
   ```

   O manualmente:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=[HOST]
   DB_PORT=[PORT]
   DB_DATABASE=railway
   DB_USERNAME=postgres
   DB_PASSWORD=[PASSWORD]
   DB_SSLMODE=require
   ```

6. **Ejecutar migraciones**
   ```bash
   php artisan migrate
   ```

---

## 🌟 Opción 3: Neon (PostgreSQL Serverless)

### ✅ Ventajas
- **512MB GRATIS** (generoso)
- PostgreSQL serverless (pausa automáticamente)
- Branching de base de datos (como Git)
- Muy rápido

### 📝 Pasos para Configurar Neon

1. **Crear cuenta en Neon**
   - Ve a: https://neon.tech
   - Inicia sesión con GitHub

2. **Crear proyecto**
   - Haz clic en "Create Project"
   - Nombre: `diario-personal`
   - Región: Elige la más cercana
   - Plan: **Free**

3. **Obtener cadena de conexión**
   - En el dashboard, copia la cadena de conexión
   - Formato: `postgresql://[user]:[password]@[host]/[database]`

4. **Configurar .env**
   ```env
   DB_URL=postgresql://[user]:[password]@[host]/[database]
   ```

   O manualmente:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=[host]
   DB_PORT=5432
   DB_DATABASE=[database]
   DB_USERNAME=[user]
   DB_PASSWORD=[password]
   DB_SSLMODE=require
   ```

---

## 📊 Comparación Rápida

| Característica | Supabase | Railway | Neon |
|---------------|----------|---------|------|
| **Espacio gratis** | 500MB | $5 crédito/mes | 512MB |
| **Backups** | ✅ Diarios | ✅ Incluidos | ✅ Incluidos |
| **SSL** | ✅ | ✅ | ✅ |
| **Dashboard** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| **Facilidad** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| **Recomendado para** | Producción | Desarrollo/Prod | Producción |

---

## 🎯 Recomendación Final

**Para producción:** Usa **Supabase**
- Más espacio gratis (500MB)
- Backups automáticos
- Dashboard excelente
- Muy confiable

**Para desarrollo rápido:** Usa **Railway**
- Más fácil de configurar
- Deploy automático
- Perfecto para prototipos

---

## ⚙️ Configuración del Proyecto

El proyecto ya está configurado para PostgreSQL. Solo necesitas:

1. **Actualizar .env** con las credenciales de tu proveedor
2. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

### Ejemplo de .env completo (Supabase):

```env
APP_NAME="Diario Personal"
APP_ENV=production
APP_KEY=base64:tu-key-generada
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña_segura
DB_SSLMODE=require

# O usa la URL completa:
# DB_URL=postgresql://postgres:password@db.xxxxx.supabase.co:5432/postgres
```

---

## 🔧 Solución de Problemas

### Error: "could not connect to server"

- Verifica que el host, puerto y credenciales sean correctos
- Asegúrate de que `DB_SSLMODE=require` esté configurado
- Verifica que tu IP esté permitida (algunos proveedores requieren whitelist)

### Error: "password authentication failed"

- Verifica la contraseña en el dashboard
- Algunos proveedores requieren resetear la contraseña

### Error: "database does not exist"

- Usa el nombre de base de datos correcto (generalmente `postgres` o `railway`)

---

## 📚 Recursos

- [Supabase Docs](https://supabase.com/docs)
- [Railway Docs](https://docs.railway.app)
- [Neon Docs](https://neon.tech/docs)
- [Laravel Database Docs](https://laravel.com/docs/database)

---

## ✅ Checklist de Configuración

- [ ] Cuenta creada en el proveedor elegido
- [ ] Base de datos creada
- [ ] Credenciales copiadas
- [ ] `.env` configurado con las credenciales
- [ ] `DB_SSLMODE=require` agregado
- [ ] Migraciones ejecutadas: `php artisan migrate`
- [ ] Conexión verificada: `php artisan tinker` → `DB::connection()->getPdo();`

¡Listo para usar en producción! 🚀

