# 🔧 Instalar Extensiones de PostgreSQL en PHP (Windows)

## Problema

PHP no tiene instaladas las extensiones `pdo_pgsql` y `pgsql` necesarias para conectarse a PostgreSQL.

## Solución

### Paso 1: Descargar las DLLs de PostgreSQL

1. **Ve a:** https://pecl.php.net/package/pdo_pgsql
2. **Descarga la versión compatible con tu PHP:**
   - PHP 8.3 → Busca "8.3" en las versiones
   - O ve directamente a: https://windows.php.net/downloads/pecl/releases/pdo_pgsql/

3. **También necesitas:**
   - `php_pdo_pgsql.dll`
   - `php_pgsql.dll`

### Paso 2: Instalar con Scoop (MÁS FÁCIL)

Si tienes Scoop instalado:

```powershell
scoop install php
scoop install postgresql
```

Luego habilita las extensiones en `php.ini`.

### Paso 3: Instalación Manual

1. **Ubicación de PHP:** `C:\php\ext\`

2. **Descargar DLLs:**
   - Ve a: https://windows.php.net/downloads/pecl/releases/pdo_pgsql/
   - Descarga: `php_pdo_pgsql-8.3.x-nts-vs16-x64.zip`
   - Extrae `php_pdo_pgsql.dll` a `C:\php\ext\`

   - Ve a: https://windows.php.net/downloads/pecl/releases/pgsql/
   - Descarga: `php_pgsql-8.3.x-nts-vs16-x64.zip`
   - Extrae `php_pgsql.dll` a `C:\php\ext\`

3. **Habilitar en php.ini:**
   - Encuentra tu `php.ini` (ejecuta: `php --ini`)
   - Abre `php.ini` con un editor de texto
   - Busca las líneas (quita el `;` si están comentadas):
     ```ini
     extension=pdo_pgsql
     extension=pgsql
     ```
   - Si no existen, agrégalas al final del archivo

4. **Reiniciar** cualquier servidor PHP o terminal

### Paso 4: Verificar Instalación

```powershell
php -m | Select-String "pgsql"
```

Deberías ver:
```
pdo_pgsql
pgsql
```

## Alternativa: Usar XAMPP o Laragon

Si prefieres una solución más fácil:

### Laragon (Recomendado para Windows)

1. Descarga: https://laragon.org/download/
2. Instala Laragon
3. Laragon incluye PHP con todas las extensiones habilitadas
4. Usa el PHP de Laragon en tu proyecto

### XAMPP

1. Descarga: https://www.apachefriends.org/
2. Instala XAMPP
3. Habilita las extensiones en `php.ini` de XAMPP

## Verificar que Funciona

```powershell
php -r "var_dump(extension_loaded('pdo_pgsql'));"
php -r "var_dump(extension_loaded('pgsql'));"
```

Ambos deberían mostrar `bool(true)`.

## Nota sobre el Error de Conexión

También veo que tu `.env` tiene el host incorrecto. Debe ser:

```env
DB_HOST=rwpedampzfzbtfqsfdnw.supabase.co
```

**NO** debe tener `https://` al inicio.

