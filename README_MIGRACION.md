# 🚀 Migración Rápida a Supabase

## Pasos para Migrar tu Base de Datos de Render a Supabase

### 📋 Requisitos Previos
- PostgreSQL instalado (para usar `pg_dump` y `psql`)
- Cuenta en [Supabase](https://supabase.com)
- Acceso a tu base de datos actual en Render

---

## 🎯 Proceso Completo

### 1️⃣ Exportar datos desde Render (si tienes datos)
```powershell
.\EXPORTAR_DESDE_RENDER.ps1
```
Este script te guiará para hacer un backup de tu base de datos actual.

### 2️⃣ Crear proyecto en Supabase
1. Ve a https://supabase.com
2. Crea un nuevo proyecto
3. Guarda las credenciales (host, password, etc.)

### 3️⃣ Importar datos a Supabase (si hiciste backup)
```powershell
.\IMPORTAR_A_SUPABASE.ps1
```
Este script importará tu backup a Supabase.

### 4️⃣ Configurar Laravel para usar Supabase
```powershell
.\CONFIGURAR_SUPABASE.ps1
```
Este script actualizará tu archivo `.env` con las credenciales de Supabase.

### 5️⃣ Probar la conexión
```bash
php artisan migrate:status
```

---

## 🆕 Si NO tienes datos en Render

Si estás empezando desde cero o no necesitas los datos antiguos:

1. **Crear proyecto en Supabase** (paso 2 de arriba)

2. **Configurar Laravel**:
   ```powershell
   .\CONFIGURAR_SUPABASE.ps1
   ```

3. **Ejecutar migraciones**:
   ```bash
   php artisan migrate:fresh --seed
   ```

---

## 📝 Configuración Manual del .env

Si prefieres configurar manualmente, edita tu archivo `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=tu-proyecto.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=tu-contraseña-supabase
DB_SSLMODE=require

# O usa la URL completa:
DATABASE_URL=postgresql://postgres:[PASSWORD]@[HOST]:5432/postgres?sslmode=require
```

---

## 🌐 Actualizar Producción

Si despliegas en algún servicio (Vercel, Railway, Netlify, etc.):

1. Ve a las variables de entorno de tu servicio
2. Actualiza las credenciales de base de datos con las de Supabase
3. Redeploya tu aplicación

---

## 📚 Documentación Completa

Para más detalles, consulta:
- [MIGRACION_SUPABASE.md](MIGRACION_SUPABASE.md) - Guía detallada completa
- [Documentación de Supabase](https://supabase.com/docs)

---

## ✅ Verificación Post-Migración

Ejecuta estos comandos para verificar que todo funciona:

```bash
# Ver estado de migraciones
php artisan migrate:status

# Probar conexión
php artisan tinker
>>> DB::connection()->getPdo();
>>> User::count();

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 🆘 Solución de Problemas

### Error de SSL
```env
DB_SSLMODE=require
```

### Timeout de Conexión
Verifica tu firewall o que la IP esté permitida en Supabase.

### Datos no aparecen
1. Verifica que la importación fue exitosa
2. Revisa las tablas en Supabase Studio
3. Ejecuta `php artisan migrate:status`

---

## 💡 Ventajas de Supabase

✅ Mejor rendimiento  
✅ Panel de administración superior (Supabase Studio)  
✅ API REST automática  
✅ Autenticación integrada  
✅ Storage de archivos  
✅ Realtime (websockets)  
✅ Backups automáticos (planes pagos)  

---

## 🎉 ¡Listo!

Una vez completada la migración, tu aplicación estará usando Supabase como base de datos.

¿Problemas? Revisa [MIGRACION_SUPABASE.md](MIGRACION_SUPABASE.md) para más detalles.
