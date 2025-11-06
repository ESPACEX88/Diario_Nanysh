# Guía de PlanetScale para este Proyecto

## 🚀 Configuración Inicial

### 1. Instalar PlanetScale CLI

**Windows:**
```powershell
# Con Scoop
scoop install pscale

# O descarga manual desde:
# https://github.com/planetscale/cli/releases
```

**Mac:**
```bash
brew install planetscale/tap/pscale
```

**Linux:**
```bash
# Descarga el binario desde GitHub
wget https://github.com/planetscale/cli/releases/latest/download/pscale_0.xxx.xxx_linux_amd64.tar.gz
tar -xzf pscale_*.tar.gz
sudo mv pscale /usr/local/bin/
```

### 2. Autenticarse

```bash
pscale auth login
```

Esto abrirá tu navegador para autenticarte.

### 3. Crear Base de Datos

```bash
pscale database create diario --region us-east
```

Regiones disponibles:
- `us-east` (Norteamérica Este)
- `us-west` (Norteamérica Oeste)
- `eu-west` (Europa Oeste)
- `ap-south` (Asia Pacífico Sur)

### 4. Crear Branch de Desarrollo

```bash
pscale branch create diario dev
```

### 5. Conectar al Branch

```bash
pscale connect diario dev --port 3306
```

Esto creará un túnel local. Mantén esta terminal abierta.

### 6. Obtener Credenciales de Conexión

1. Ve a https://app.planetscale.com
2. Selecciona tu base de datos `diario`
3. Haz clic en "Connect"
4. Selecciona "General" o "Prisma"
5. Copia las credenciales

### 7. Configurar .env

```env
DB_CONNECTION=mysql
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=false
```

**Nota:** Si usas el túnel local (`pscale connect`), usa:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
```

## 📝 Trabajar con Migraciones

### Aplicar Migraciones

1. **Conectar al branch** (en una terminal):
   ```bash
   pscale connect diario dev --port 3306
   ```

2. **Ejecutar migraciones** (en otra terminal):
   ```bash
   php artisan migrate
   ```

### Crear Nueva Migración

```bash
php artisan make:migration nombre_de_la_migracion
```

**IMPORTANTE:** PlanetScale no soporta:
- ❌ Foreign keys
- ❌ Triggers
- ❌ Stored procedures
- ❌ Views

Usa índices en lugar de foreign keys para mantener la integridad referencial a nivel de aplicación.

## 🌿 Branching (Ramas)

### Crear un Branch

```bash
pscale branch create diario nombre-del-branch
```

### Listar Branches

```bash
pscale branch list diario
```

### Cambiar de Branch

```bash
pscale branch switch diario nombre-del-branch
```

### Eliminar un Branch

```bash
pscale branch delete diario nombre-del-branch
```

## 🚢 Deploy a Producción

### 1. Crear Branch de Producción

```bash
pscale branch create diario main
```

### 2. Aplicar Migraciones en Producción

```bash
# Conectar a producción
pscale connect diario main --port 3307

# En otra terminal
php artisan migrate
```

### 3. Promover Branch a Producción

```bash
pscale branch promote diario main
```

Esto hace que `main` sea el branch de producción.

## 🔄 Workflow Recomendado

1. **Desarrollo:**
   ```bash
   # Trabajar en branch dev
   pscale connect diario dev --port 3306
   php artisan migrate
   ```

2. **Testing:**
   ```bash
   # Crear branch de testing
   pscale branch create diario test
   pscale connect diario test --port 3308
   php artisan migrate
   ```

3. **Producción:**
   ```bash
   # Promover a main
   pscale branch promote diario main
   ```

## 🔍 Verificar Estado

```bash
# Ver información de la base de datos
pscale database show diario

# Ver branches
pscale branch list diario

# Ver conexiones activas
pscale connect diario dev --port 3306 --status
```

## ⚠️ Limitaciones de PlanetScale

1. **Sin Foreign Keys:**
   - Las migraciones ya están adaptadas
   - Usa índices para mejorar rendimiento
   - Mantén integridad a nivel de aplicación

2. **Sin Migraciones Rollback:**
   - No puedes hacer `php artisan migrate:rollback`
   - Crea un nuevo branch si necesitas revertir cambios

3. **Sin DDL en Transacciones:**
   - No puedes hacer DDL (CREATE, ALTER, DROP) en transacciones
   - Cada statement se ejecuta inmediatamente

## 🆘 Solución de Problemas

### Error de Conexión SSL

Si obtienes errores SSL, descarga el certificado:

```bash
# Windows PowerShell
Invoke-WebRequest -Uri "https://github.com/planetscale/mysql-proxy/releases/download/v1.0.0/cacert.pem" -OutFile "cacert.pem"
```

Luego en `.env`:
```env
DB_SSL_CA=C:/ruta/completa/a/cacert.pem
```

### Puerto Ya en Uso

Si el puerto 3306 está ocupado, usa otro:

```bash
pscale connect diario dev --port 3307
```

Y actualiza `.env`:
```env
DB_PORT=3307
```

### Ver Logs

```bash
pscale logs diario
```

## 📚 Recursos

- [Documentación de PlanetScale](https://docs.planetscale.com)
- [PlanetScale CLI](https://github.com/planetscale/cli)
- [Laravel + PlanetScale](https://docs.planetscale.com/tutorials/laravel)

