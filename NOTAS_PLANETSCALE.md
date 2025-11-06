# ⚠️ Notas Importantes sobre PlanetScale

## Cambios Realizados en el Proyecto

### 1. Migraciones Adaptadas

Todas las migraciones han sido actualizadas para ser compatibles con PlanetScale:

- ✅ **Foreign Keys Removidas:** PlanetScale no soporta foreign keys
- ✅ **Índices Agregados:** Se agregaron índices en todas las columnas que antes tenían foreign keys
- ✅ **Sin Constraints:** Las relaciones se mantienen a nivel de aplicación (Eloquent)

### 2. Tablas Afectadas

Las siguientes tablas fueron modificadas:

- `diary_entries` - `user_id` ahora es `unsignedBigInteger` con índice
- `notes` - `user_id` ahora es `unsignedBigInteger` con índice
- `albums` - `user_id` y `cover_photo_id` ahora son `unsignedBigInteger` con índices
- `photos` - `user_id` y `album_id` ahora son `unsignedBigInteger` con índices
- `goals` - `user_id` ahora es `unsignedBigInteger` con índice
- `habits` - `user_id` ahora es `unsignedBigInteger` con índice
- `habit_logs` - `habit_id` y `user_id` ahora son `unsignedBigInteger` con índices
- `gratitudes` - `user_id` ahora es `unsignedBigInteger` con índice
- `user_settings` - `user_id` ahora es `unsignedBigInteger` con índice único
- `taggables` - `tag_id` ahora es `unsignedBigInteger` con índice

### 3. Integridad Referencial

**IMPORTANTE:** Sin foreign keys, la integridad referencial debe manejarse a nivel de aplicación:

- ✅ Los modelos Eloquent mantienen las relaciones
- ✅ Usa `onDelete('cascade')` en los modelos para eliminar relaciones
- ⚠️ Debes validar manualmente que los IDs existan antes de insertar
- ⚠️ Considera usar Observers o Events para limpiar datos huérfanos

### 4. Configuración de Base de Datos

El archivo `config/database.php` ha sido actualizado:

- Default connection: `mysql` (en lugar de `sqlite`)
- Soporte para SSL de PlanetScale
- Configuración de verificación SSL deshabilitada por defecto

### 5. Archivos de Documentación

Se crearon los siguientes archivos:

- `PLANETSCALE.md` - Guía completa de uso de PlanetScale
- `NOTAS_PLANETSCALE.md` - Este archivo con notas técnicas
- `README.md` - Actualizado con instrucciones de PlanetScale

## ⚠️ Consideraciones Importantes

### No Puedes Hacer Rollback

PlanetScale no soporta `migrate:rollback`. Si necesitas revertir cambios:

1. Crea un nuevo branch desde el estado anterior
2. O crea una nueva migración que revierta los cambios

### Validación Manual

Sin foreign keys, debes validar manualmente:

```php
// ❌ Antes (con foreign keys, automático)
$entry = DiaryEntry::create([
    'user_id' => 999, // Si no existe, falla automáticamente
]);

// ✅ Ahora (sin foreign keys, validar manualmente)
$user = User::find($request->user_id);
if (!$user) {
    throw new \Exception('User not found');
}

$entry = DiaryEntry::create([
    'user_id' => $request->user_id,
]);
```

### Limpieza de Datos Huérfanos

Considera crear un comando Artisan para limpiar datos huérfanos:

```php
// Ejemplo: Limpiar fotos sin usuario
Photo::whereDoesntHave('user')->delete();
```

## ✅ Ventajas de PlanetScale

1. **Branching:** Crea branches de BD como en Git
2. **Escalabilidad:** Infraestructura muy rápida y escalable
3. **Sin DOWNTIME:** Migraciones sin downtime
4. **Gratis:** Hasta 5GB gratis
5. **Backups:** Automáticos

## 📝 Próximos Pasos

1. Configura tu cuenta de PlanetScale
2. Crea la base de datos
3. Conecta usando `pscale connect`
4. Ejecuta las migraciones
5. ¡Listo para desarrollar!

