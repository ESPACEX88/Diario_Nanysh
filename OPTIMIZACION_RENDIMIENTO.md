# Optimización de Rendimiento - Aplicación Laravel

## Mejoras Implementadas

### 1. Índices de Base de Datos

Se ha creado una migración (`2025_04_24_000001_add_performance_indexes.php`) que agrega índices compuestos para optimizar las consultas más frecuentes:

#### Índices Creados:

- **diary_entries**: `idx_diary_user_favorite` - (user_id, is_favorite, deleted_at)
  - Optimiza conteos de entradas favoritas por usuario
  
- **notes**: `idx_notes_user_pinned` - (user_id, is_pinned, deleted_at)
  - Optimiza consulta de notas fijadas
  
- **goals**: `idx_goals_user_active` - (user_id, is_completed, deleted_at)
  - Optimiza conteo de metas activas
  
- **habits**: `idx_habits_user_active` - (user_id, is_active, deleted_at)
  - Optimiza lista de hábitos activos
  
- **gratitudes**: `idx_gratitudes_user_date` - (user_id, date, deleted_at)
  - Optimiza filtrado por fecha y usuario
  
- **todos**: `idx_todos_user_completed_due` - (user_id, is_completed, due_date, priority)
  - Optimiza lista de tareas pendientes ordenadas
  
- **events**: `idx_events_user_start` - (user_id, start_date, deleted_at)
  - Optimiza eventos próximos

- **Full-text search** (PostgreSQL): `idx_diary_search`
  - Búsqueda de texto completo en título y contenido de entradas del diario

### 2. Caché en Dashboard

El `DashboardController` ya implementa caché para estadísticas:

```php
// Caché de 5 minutos para estadísticas generales
$stats = Cache::remember("dashboard.stats.{$user->id}", 300, function () { ... });

// Caché de 10 minutos para estadísticas semanales
$weekStats = Cache::remember("dashboard.week.{$user->id}", 600, function () { ... });
```

### 3. Eager Loading

Se utiliza eager loading para evitar el problema N+1:

```php
DiaryEntry::where('user_id', $user->id)
    ->with(['tags'])
    ->orderBy('date', 'desc')
    ->limit(5)
    ->get();
```

## Recomendaciones Adicionales

### 1. Configurar Redis para Caché (Recomendado)

Para mejorar el rendimiento del caché, configura Redis:

```env
CACHE_STORE=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Instalar extensión Redis:
```bash
pecl install redis
```

### 2. Optimizar Consultas Existentes

#### DiaryEntryController - Método index:
```php
// Actual: Ya usa eager loading correctamente
$query = DiaryEntry::where('user_id', Auth::id())
    ->with(['tags', 'photos'])
    ->orderBy('date', 'desc');

// Mejora adicional: Usar select() para traer solo columnas necesarias
$query = DiaryEntry::where('user_id', Auth::id())
    ->select(['id', 'user_id', 'title', 'content', 'mood', 'date', 'is_favorite', 'created_at'])
    ->with(['tags:id,name', 'photos:id,photoable_id,url'])
    ->orderBy('date', 'desc');
```

### 3. Paginación Optimizada

Para listas grandes, considerar paginación simple:
```php
// En lugar de paginate() que hace COUNT(), usar simplePaginate()
$entries = $query->simplePaginate(15);
```

### 4. Queue para Tareas Pesadas

Mover a cola de background:
- Envío de emails
- Procesamiento de imágenes
- Exportación de datos
- Verificación de logros

Ejemplo:
```php
// En lugar de ejecutar directamente
AchievementService::checkDiaryAchievements(Auth::user());

// Usar job en cola
CheckAchievementsJob::dispatch(Auth::user());
```

### 5. Configurar OPcache

En `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### 6. Habilitar HTTP/2

Configurar en nginx/Apache para servir assets con HTTP/2.

### 7. Compresión de Assets

Vite ya está configurado para producción, asegurar:
```bash
npm run build
```

### 8. Optimizar Autoloader

```bash
composer dump-autoload --optimize --classmap-authoritative
```

### 9. Configurar Database Pooling

Para PostgreSQL en production, configurar pgBouncer o similar.

### 10. Monitoreo

Implementar:
- Laravel Telescope (desarrollo)
- Sentry o Bugsnag (producción)
- Query logging lento:
```php
// En AppServiceProvider
DB::listen(function ($query) {
    if ($query->time > 100) {
        Log::warning("Slow query: {$query->sql}");
    }
});
```

## Pasos para Aplicar las Mejoras

1. **Ejecutar migración de índices:**
   ```bash
   php artisan migrate
   ```

2. **Limpiar y optimizar caché:**
   ```bash
   php artisan cache:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Optimizar autoloader:**
   ```bash
   composer dump-autoload --optimize
   ```

4. **Rebuild assets:**
   ```bash
   npm run build
   ```

## Métricas de Mejora Esperadas

- **Dashboard load time**: 40-60% más rápido con caché
- **Consultas con índices**: 10-100x más rápidas
- **Búsqueda de texto**: 50-100x más rápida con GIN index
- **N+1 queries**: Eliminadas con eager loading

## Notas Importantes

- Los índices aumentan el tamaño de la base de datos pero mejoran significativamente las lecturas
- El caché debe invalidarse cuando se actualizan datos críticos
- Monitorear el uso de memoria del caché
- Considerar cache warming para datos frecuentemente accedidos
