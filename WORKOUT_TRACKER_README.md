# 💪 Sistema de Seguimiento de Entrenamientos

## Descripción
Sistema completo para registrar y visualizar entrenamientos del gimnasio con un calendario visual interactivo.

## Características

### 📅 Calendario Visual
- Calendario mensual con colores que indican:
  - 🟢 **Verde**: Entrenamientos ligeros
  - 🟡 **Amarillo**: Entrenamientos moderados
  - 🔴 **Rojo**: Entrenamientos intensos
- Los días con entrenamiento muestran un punto de color y la duración
- Clic en un día con entrenamiento para ver los detalles
- Clic en un día vacío para crear un nuevo entrenamiento

### 📊 Estadísticas
- **Total de entrenamientos**: Cuenta de entrenamientos del mes actual
- **Minutos totales**: Suma de todos los minutos entrenados
- **Racha actual**: Días consecutivos de entrenamiento

### ✍️ Registro de Entrenamientos
Cada entrenamiento puede incluir:
- **Fecha**: Día del entrenamiento
- **Nombre de rutina**: Ej. "Piernas", "Tren Superior", "Cardio"
- **Intensidad**: Ligero, Moderado o Intenso
- **Duración**: Tiempo en minutos
- **Ejercicios**: Lista detallada con:
  - Nombre del ejercicio
  - Series
  - Repeticiones
  - Peso utilizado
- **Notas**: Observaciones adicionales

## Uso

### Acceso
Puedes acceder desde el menú principal:
- **Navegación Desktop**: Menú "Más" → Sección "📝 Registros" → "💪 Gym"
- **Navegación Móvil**: Menú hamburguesa → "💪 Gym"

### Registrar un Entrenamiento
1. Haz clic en "Registrar entrenamiento" o en un día vacío del calendario
2. Completa los campos:
   - Fecha (por defecto: hoy o día seleccionado)
   - Nombre de la rutina
   - Intensidad (selecciona una de las 3 opciones)
   - Duración en minutos (opcional)
3. Agrega ejercicios:
   - Haz clic en "+ Agregar ejercicio"
   - Completa nombre, series, reps y peso
   - Puedes agregar múltiples ejercicios
4. Agrega notas adicionales si lo deseas
5. Haz clic en "Guardar Entrenamiento"

**Nota**: Solo puedes registrar un entrenamiento por día.

### Ver Entrenamientos
- **Vista de Calendario**: Visualiza todos los entrenamientos del mes con colores
- **Lista Reciente**: Debajo del calendario ves los 5 entrenamientos más recientes
- **Detalles**: Haz clic en un día con entrenamiento para ver todos los detalles en un modal

### Editar un Entrenamiento
1. Haz clic en "Editar" en la lista de entrenamientos o en el modal de detalles
2. Modifica los campos que desees
3. Puedes agregar o eliminar ejercicios
4. Haz clic en "Guardar cambios"

### Eliminar un Entrenamiento
1. En la página de edición, haz clic en el botón rojo "Eliminar"
2. Confirma la eliminación en el modal
3. El entrenamiento será eliminado permanentemente

## Navegación por Meses
- Usa las flechas ← → en el calendario para cambiar de mes
- Las estadísticas se actualizan automáticamente según el mes seleccionado

## Consejos de Uso
- 🎯 Mantén una racha constante para ver crecer tu contador de días
- 📝 Usa las notas para registrar cómo te sentiste o ajustes importantes
- 💪 Detalla los ejercicios para hacer seguimiento de tu progreso
- 🎨 Los colores de intensidad te ayudan a balancear tu rutina visualmente

## Tecnología
- **Backend**: Laravel 10 + Eloquent ORM
- **Frontend**: Vue 3 + Inertia.js
- **Estilos**: Tailwind CSS
- **Base de datos**: SQLite (tabla: workout_logs)
