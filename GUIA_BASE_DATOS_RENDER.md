# 🗄️ Guía: Ver y Editar Base de Datos en Render

## 📋 Opciones Disponibles

### 1. **Usando Render Shell (Laravel Tinker)** ⭐ RECOMENDADO

La forma más fácil es usar Laravel Tinker desde Render Shell:

#### Pasos:
1. Ve a tu dashboard de Render: https://dashboard.render.com
2. Selecciona tu servicio web (no la base de datos)
3. Click en **"Shell"** (en el menú lateral)
4. Ejecuta los siguientes comandos:

```bash
# Ver todas las tablas
php artisan db:show

# Ver estructura de una tabla específica
php artisan db:table nombre_tabla

# Abrir Tinker (consola interactiva de Laravel)
php artisan tinker
```

#### Ejemplos en Tinker:

```php
// Ver todos los usuarios
\App\Models\User::all();

// Ver un usuario específico
\App\Models\User::find(1);

// Ver todas las entradas del diario
\App\Models\DiaryEntry::all();

// Ver todas las tareas
\App\Models\Todo::all();

// Ver todas las frases motivacionales
\App\Models\MotivationalQuote::all();

// Ver todos los logros
\App\Models\Achievement::all();

// Crear un nuevo logro
\App\Models\Achievement::create([
    'code' => 'test_achievement',
    'name' => 'Logro de Prueba',
    'description' => 'Descripción del logro',
    'icon' => '🎉',
    'color' => '#F472B6',
    'points' => 10,
    'type' => 'diary',
    'requirement_value' => 1,
]);

// Actualizar un registro
$user = \App\Models\User::find(1);
$user->name = 'Nuevo Nombre';
$user->save();

// Eliminar un registro
\App\Models\Todo::find(1)->delete();

// Contar registros
\App\Models\DiaryEntry::count();
```

### 2. **Usando Herramientas Externas (pgAdmin, DBeaver, etc.)**

Puedes conectarte directamente a la base de datos PostgreSQL usando herramientas gráficas:

#### Obtener Credenciales de Conexión:
1. Ve a tu dashboard de Render
2. Selecciona tu **base de datos PostgreSQL**
3. En la sección **"Connections"** verás:
   - **Host**: `dpg-xxxxx-a.oregon-postgres.render.com`
   - **Port**: `5432`
   - **Database**: `nombre_base_datos`
   - **User**: `usuario`
   - **Password**: (click en "Show" para verla)

#### Usando pgAdmin (Gratis):
1. Descarga pgAdmin: https://www.pgadmin.org/download/
2. Instala y abre pgAdmin
3. Click derecho en "Servers" → "Create" → "Server"
4. En la pestaña **"General"**:
   - Name: `Render Database`
5. En la pestaña **"Connection"**:
   - Host: `dpg-xxxxx-a.oregon-postgres.render.com`
   - Port: `5432`
   - Database: `nombre_base_datos`
   - Username: `usuario`
   - Password: `tu_password`
6. Click en "Save"
7. Ahora puedes explorar todas las tablas, ver datos, editar, etc.

#### Usando DBeaver (Gratis):
1. Descarga DBeaver: https://dbeaver.io/download/
2. Instala y abre DBeaver
3. Click en "New Database Connection"
4. Selecciona **PostgreSQL**
5. Ingresa las credenciales de Render
6. Click en "Test Connection" y luego "Finish"
7. Explora y edita datos visualmente

### 3. **Usando Comandos SQL desde Render Shell**

También puedes ejecutar SQL directamente:

```bash
# Conectarte a PostgreSQL desde Shell
psql $DATABASE_URL

# O si tienes las variables separadas:
psql -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE
```

#### Ejemplos de SQL:

```sql
-- Ver todas las tablas
\dt

-- Ver estructura de una tabla
\d nombre_tabla

-- Ver todos los usuarios
SELECT * FROM users;

-- Ver todas las entradas del diario
SELECT * FROM diary_entries;

-- Ver todas las tareas
SELECT * FROM todos;

-- Insertar un nuevo registro
INSERT INTO todos (user_id, title, description, is_completed, created_at, updated_at)
VALUES (1, 'Nueva Tarea', 'Descripción', false, NOW(), NOW());

-- Actualizar un registro
UPDATE todos SET title = 'Tarea Actualizada' WHERE id = 1;

-- Eliminar un registro
DELETE FROM todos WHERE id = 1;

-- Ver conteos
SELECT COUNT(*) FROM diary_entries;
SELECT COUNT(*) FROM todos WHERE is_completed = true;
```

### 4. **Usando Laravel Migrations y Seeders**

Para modificar la estructura o datos iniciales:

```bash
# Desde Render Shell

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Ejecutar un seeder específico
php artisan db:seed --class=AchievementSeeder

# Refrescar base de datos (CUIDADO: elimina todos los datos)
php artisan migrate:fresh --seed
```

## 🔍 Comandos Útiles para Ver Datos

### Desde Render Shell:

```bash
# Ver todas las rutas
php artisan route:list

# Ver configuración de base de datos
php artisan config:show database

# Ver logs
php artisan log:show

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Desde Tinker:

```php
// Ver estadísticas generales
\App\Models\DiaryEntry::count();
\App\Models\Todo::count();
\App\Models\Event::count();
\App\Models\WishlistItem::count();

// Ver últimas entradas
\App\Models\DiaryEntry::latest()->take(5)->get();

// Ver tareas pendientes
\App\Models\Todo::where('is_completed', false)->get();

// Ver eventos próximos
\App\Models\Event::where('start_date', '>=', now())->orderBy('start_date')->get();

// Ver mascota
\App\Models\Pet::first();
```

## ⚠️ Precauciones

1. **Siempre haz backup antes de modificar datos importantes**
2. **No elimines datos sin estar seguro**
3. **Usa transacciones para cambios grandes**
4. **Verifica que los cambios sean correctos antes de guardar**

## 🎯 Casos de Uso Comunes

### Ver todas las entradas del diario:
```php
php artisan tinker
\App\Models\DiaryEntry::all();
```

### Agregar una frase motivacional:
```php
php artisan tinker
\App\Models\MotivationalQuote::create([
    'quote' => 'Tu frase aquí',
    'author' => 'Autor',
    'category' => 'motivation',
    'is_active' => true,
]);
```

### Ver el estado de Snoopy:
```php
php artisan tinker
\App\Models\Pet::first();
```

### Ver todos los logros disponibles:
```php
php artisan tinker
\App\Models\Achievement::all();
```

## 📝 Notas

- **Render Shell** es la forma más fácil y segura
- **pgAdmin/DBeaver** son mejores para ediciones visuales complejas
- **Tinker** es perfecto para operaciones rápidas de Laravel
- **SQL directo** es útil para consultas complejas


