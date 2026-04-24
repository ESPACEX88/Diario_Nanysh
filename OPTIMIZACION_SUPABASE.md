# Optimización de Rendimiento para Supabase

## Configuración Esencial

### 1. Connection Pooling con Supavisor

Supabase incluye **Supavisor** para connection pooling. Configura tu `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=db.[tu-project-ref].supabase.co
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[tu-service-role-key]
DB_SSLMODE=require
```

**Puertos disponibles:**
- `5432` - Conexión directa (desarrollo)
- `6543` - Transaction mode pooling (producción, recomendado)

### 2. Índices ya Creados

La migración `2025_04_24_000001_add_performance_indexes.php` incluye:

| Tabla | Índice | Columnas | Propósito |
|-------|--------|----------|-----------|
| diary_entries | idx_diary_user_favorite | user_id, is_favorite, deleted_at | Entradas favoritas |
| notes | idx_notes_user_pinned | user_id, is_pinned, deleted_at | Notas fijadas |
| goals | idx_goals_user_active | user_id, is_completed, deleted_at | Metas activas |
| habits | idx_habits_user_active | user_id, is_active, deleted_at | Hábitos activos |
| gratitudes | idx_gratitudes_user_date | user_id, date, deleted_at | Filtrado por fecha |
| todos | idx_todos_user_completed_due | user_id, is_completed, due_date, priority | Tareas pendientes |
| events | idx_events_user_start | user_id, start_date, deleted_at | Eventos próximos |
| diary_entries | idx_diary_search | GIN full-text | Búsqueda texto |

### 3. Verificar Índices en Supabase

Ejecuta en el SQL Editor de Supabase:

```sql
SELECT 
    tablename,
    indexname,
    indexdef
FROM pg_indexes 
WHERE schemaname = 'public'
ORDER BY tablename, indexname;
```

## Optimizaciones Recomendadas

### 1. Habilitar Extensiones PostgreSQL

```sql
-- En SQL Editor de Supabase
CREATE EXTENSION IF NOT EXISTS pg_trgm;  -- Búsquedas fuzzy
CREATE EXTENSION IF NOT EXISTS btree_gin; -- Índices compuestos avanzados
```

### 2. Row Level Security (RLS)

Si usas autenticación de Laravel (no Supabase Auth):

```sql
-- Deshabilitar RLS para tablas usadas por Laravel
ALTER TABLE diary_entries DISABLE ROW LEVEL SECURITY;
ALTER TABLE notes DISABLE ROW LEVEL SECURITY;
ALTER TABLE goals DISABLE ROW LEVEL SECURITY;
ALTER TABLE habits DISABLE ROW LEVEL SECURITY;
ALTER TABLE todos DISABLE ROW LEVEL SECURITY;
```

O usa el `service_role_key` que bypassa RLS automáticamente.

### 3. Query Performance Monitoring

Revisa en Supabase Dashboard:
- **Database** → **Query Performance**
- **Settings** → **Logs**

### 4. Caché Externo (Redis)

Supabase no incluye Redis. Opciones recomendadas:

**Upstash Redis (serverless):**
```env
CACHE_STORE=redis
REDIS_URL=rediss://default:[password]@[host]:[port]
```

### 5. Realtime Subscriptions (Opcional)

Para actualizaciones en tiempo real:

```javascript
// resources/js/app.js
import { createClient } from '@supabase/supabase-js'

const supabase = createClient(
    import.meta.env.VITE_SUPABASE_URL,
    import.meta.env.VITE_SUPABASE_ANON_KEY
)

// Suscribirse a cambios
supabase
    .channel('changes')
    .on('postgres_changes', 
        { event: '*', schema: 'public', table: 'diary_entries' },
        (payload) => console.log(payload)
    )
    .subscribe()
```

## Pasos de Implementación

1. **Actualizar .env con pooling:**
   ```bash
   DB_PORT=6543
   ```

2. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

3. **Verificar índices:**
   ```sql
   SELECT indexname FROM pg_indexes WHERE schemaname = 'public';
   ```

4. **Optimizar configuración Laravel:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   composer dump-autoload --optimize
   ```

## Métricas Esperadas

| Operación | Sin optimizar | Con optimización | Mejora |
|-----------|---------------|------------------|--------|
| Dashboard | 800ms | 300ms | 60% |
| Búsqueda texto | 1000ms | 20ms | 50x |
| Listado con filtros | 500ms | 50ms | 10x |
| Conexiones simultáneas | 100 máx | 1000+ | 10x |

## Troubleshooting

### Error: "Too many connections"
✅ Solución: Usar puerto 6543 (Supavisor pooling)

### Error: "SSL required"
✅ Solución: `DB_SSLMODE=require` en .env

### Consultas lentas
✅ Solución: Revisar Query Performance en dashboard y agregar índices

### RLS bloquea consultas
✅ Solución: Usar service_role_key o deshabilitar RLS
