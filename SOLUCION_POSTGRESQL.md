# 🔧 Solución: Instalar Extensiones PostgreSQL en PHP (Herd Lite)

## Problema Actual

PHP no encuentra las extensiones `pdo_pgsql` y `pgsql`. El `php.ini` ya las tiene habilitadas, pero faltan las DLLs.

## Solución Rápida

### Opción 1: Usar Scoop para instalar PHP con extensiones

```powershell
scoop install php
scoop install postgresql
```

Luego actualiza tu PATH para usar el PHP de Scoop.

### Opción 2: Descargar DLLs Manualmente

1. **Ve a:** https://pecl.php.net/package/pdo_pgsql
2. **Descarga la versión para PHP 8.3 NTS x64:**
   - Busca en: https://windows.php.net/downloads/pecl/releases/
   - O usa este enlace directo (puede cambiar):
     - https://windows.php.net/downloads/pecl/releases/pdo_pgsql/

3. **Extrae las DLLs a:**
   ```
   C:\Users\posad\.config\herd-lite\bin\ext\
   ```

4. **Archivos necesarios:**
   - `php_pdo_pgsql.dll`
   - `php_pgsql.dll`

### Opción 3: Usar Laragon (MÁS FÁCIL)

1. Descarga Laragon: https://laragon.org/download/
2. Instala Laragon
3. Laragon incluye PHP con todas las extensiones
4. Usa el PHP de Laragon en lugar de Herd

### Opción 4: Habilitar en Herd

Si Herd tiene un gestor de extensiones:
1. Abre Herd
2. Ve a configuración
3. Habilita las extensiones de PostgreSQL

## ⚠️ Problema Adicional: Host Incorrecto

En tu `.env`, el host tiene `https://` cuando NO debe tenerlo:

**❌ INCORRECTO:**
```env
DB_HOST=https://rwpedampzfzbtfqsfdnw.supabase.co
```

**✅ CORRECTO:**
```env
DB_HOST=rwpedampzfzbtfqsfdnw.supabase.co
```

O mejor aún, usa la cadena de conexión completa de Supabase:

```env
DB_URL=postgresql://postgres:[PASSWORD]@rwpedampzfzbtfqsfdnw.supabase.co:5432/postgres?sslmode=require
```

## Verificar Instalación

Después de instalar las DLLs:

```powershell
php -m | Select-String "pgsql"
```

Deberías ver:
```
pdo_pgsql
pgsql
```

## Prueba de Conexión

```powershell
php artisan tinker
```

Luego en tinker:
```php
DB::connection()->getPdo();
```

Si funciona, verás información de la conexión.

