
# Diario Personal - Laravel 11 + Inertia.js + Vue 3

Una aplicación completa de diario personal construida con Laravel 11, Inertia.js, Vue 3 (Composition API) y TypeScript.

## 🚀 Características

- ✅ **Autenticación completa** con Laravel Breeze
- 📝 **CRUD de entradas de diario** con editor de texto enriquecido (Tiptap)
- 😊 **Selector de estado de ánimo** con emojis
- 📸 **Galería de fotos** con álbumes
- 📌 **Sistema de notas** tipo post-it con drag & drop
- 🎯 **Objetivos y hábitos** con tracking de progreso
- 🙏 **Sección de gratitud** diaria
- 📊 **Estadísticas y gráficas** con Chart.js
- 🎨 **Temas personalizables** y modo oscuro
- 📱 **PWA** (Progressive Web App) instalable
- 📄 **Exportación de datos** a JSON y PDF

## 📋 Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- PostgreSQL (o MySQL/SQLite para desarrollo)
- Extensiones PHP: pdo_pgsql, pgsql (o pdo_mysql, mysql para MySQL)

## 🛠️ Instalación

1. **Clonar el repositorio** (o usar el proyecto actual)

```bash
cd "Diario de Nahysh"
```

2. **Instalar dependencias de PHP**

```bash
composer install
```

3. **Instalar dependencias de Node.js**

```bash
npm install --legacy-peer-deps
```

4. **Configurar el archivo .env**

```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` y configura tu base de datos. **Recomendamos usar Supabase (gratis, 500MB):**

**Opción 1: Supabase (Recomendado - GRATIS)**
```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
DB_SSLMODE=require
```

**Opción 2: Railway (GRATIS - $5 crédito/mes)**
```env
DB_CONNECTION=pgsql
DB_HOST=[host-de-railway]
DB_PORT=[port]
DB_DATABASE=railway
DB_USERNAME=postgres
DB_PASSWORD=[password]
DB_SSLMODE=require
```

**Ver guía completa:** `GUIA_BASE_DATOS_GRATIS.md`

5. **Ejecutar migraciones**

```bash
php artisan migrate
```

6. **Crear el enlace simbólico de storage**

```bash
php artisan storage:link
```

7. **Compilar assets para desarrollo**

```bash
npm run dev
```

8. **Iniciar el servidor de desarrollo**

En otra terminal:

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## 📦 Estructura del Proyecto

```
app/
├── Http/Controllers/     # Controladores de recursos
├── Models/                  # Modelos Eloquent
├── Services/                # Servicios (ImageService, StatisticsService, ExportService)
└── Policies/                # Políticas de autorización

database/
├── migrations/              # Migraciones de base de datos
└── seeders/                 # Seeders con datos de ejemplo

resources/
├── js/
│   ├── Components/          # Componentes Vue reutilizables
│   ├── Pages/               # Páginas Inertia
│   ├── Layouts/             # Layouts de la aplicación
│   └── Composables/         # Composables de Vue
└── css/                     # Estilos Tailwind

config/
└── diary.php                # Configuración de la aplicación
```

## 🎯 Próximos Pasos

El proyecto tiene la estructura base completa. Necesitas completar:

### 1. Controladores

Los controladores están creados pero necesitan implementación. Ejemplo para `DiaryEntryController`:

```php
public function index(Request $request)
{
    $entries = auth()->user()->diaryEntries()
        ->with(['tags', 'photos'])
        ->latest('date')
        ->paginate(15);

    return Inertia::render('Diary/Index', [
        'entries' => $entries,
    ]);
}
```

### 2. Form Requests

Crea Form Requests para validación:

```bash
php artisan make:request StoreDiaryEntryRequest
php artisan make:request UpdateDiaryEntryRequest
```

### 3. Policies

Crea Policies para autorización:

```bash
php artisan make:policy DiaryEntryPolicy --model=DiaryEntry
```

### 4. Componentes Vue

Crea los componentes Vue en `resources/js/Components/`:

- `RichTextEditor.vue` - Editor Tiptap
- `MoodSelector.vue` - Selector de emojis
- `ImageUploader.vue` - Subida de imágenes
- `CalendarView.vue` - Vista de calendario
- `MoodChart.vue` - Gráfica de estados de ánimo
- `TagInput.vue` - Input de tags
- `HabitTracker.vue` - Tracker de hábitos
- `NoteCard.vue` - Tarjeta de nota
- `PhotoGallery.vue` - Galería de fotos

### 5. Páginas Vue

Crea las páginas en `resources/js/Pages/`:

- `Dashboard.vue`
- `Diary/Index.vue`, `Diary/Create.vue`, `Diary/Edit.vue`, `Diary/Show.vue`
- `Notes/Index.vue`, `Notes/Create.vue`, `Notes/Edit.vue`
- `Gallery/Index.vue`, `Gallery/Album.vue`
- `Goals/Index.vue`, `Goals/Create.vue`, `Goals/Edit.vue`
- `Habits/Index.vue`, `Habits/Create.vue`, `Habits/Edit.vue`
- `Gratitude/Index.vue`, `Gratitude/Create.vue`
- `Statistics/Index.vue`
- `Settings.vue`

### 6. Seeders

Crea seeders con datos de ejemplo:

```bash
php artisan make:seeder DiaryEntrySeeder
php artisan make:seeder NoteSeeder
php artisan make:seeder HabitSeeder
```

### 7. Comandos Artisan

Crea comandos personalizados:

```bash
php artisan make:command CleanupImages
php artisan make:command SendReminders
php artisan make:command GenerateReport
```

### 8. PWA

Configura PWA en `vite.config.js`:

```js
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
    plugins: [
        // ... otros plugins
        VitePWA({
            registerType: 'autoUpdate',
            manifest: {
                name: 'Diario Personal',
                short_name: 'Diario',
                description: 'Tu diario personal',
                theme_color: '#6366f1',
                icons: [
                    {
                        src: '/icon-192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    }
                ]
            }
        })
    ]
})
```

## 🗄️ Base de Datos - PostgreSQL (Gratis para Producción)

Este proyecto está configurado para usar **PostgreSQL** con opciones gratuitas para producción.

### 🆓 Opciones Gratuitas Recomendadas

1. **Supabase** ⭐ (RECOMENDADO)
   - 500MB GRATIS
   - Backups automáticos
   - Dashboard excelente
   - [Guía completa aquí](GUIA_BASE_DATOS_GRATIS.md#-opción-1-supabase-recomendado)

2. **Railway**
   - $5 crédito GRATIS/mes
   - Deploy automático
   - Muy fácil de usar
   - [Guía completa aquí](GUIA_BASE_DATOS_GRATIS.md#-opción-2-railway)

3. **Neon**
   - 512MB GRATIS
   - PostgreSQL serverless
   - [Guía completa aquí](GUIA_BASE_DATOS_GRATIS.md#-opción-3-neon-postgresql-serverless)

### ⚡ Configuración Rápida (Supabase)

1. **Crear cuenta:** https://supabase.com
2. **Crear proyecto** (gratis, 500MB)
3. **Copiar credenciales** del dashboard
4. **Configurar .env:**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=db.xxxxx.supabase.co
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=tu_contraseña
   DB_SSLMODE=require
   ```
5. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

**📖 Ver guía completa:** `GUIA_BASE_DATOS_GRATIS.md`

## 🎨 Personalización

### Temas

Los temas disponibles están en `config/diary.php`:

- purple
- pink
- blue
- green
- orange

### Estados de Ánimo

Los emojis de estado de ánimo están en `config/diary.php`:

- 😊 Feliz
- 😍 Enamorado
- 😎 Genial
- 😌 Tranquilo
- 😴 Cansado
- 😢 Triste
- 😤 Enojado
- 🤔 Pensativo

## 📝 Notas de Desarrollo

- Usa `npm run dev` para desarrollo con hot reload
- Usa `npm run build` para compilar para producción
- Las imágenes se guardan en `storage/app/public/photos`
- Los thumbnails se guardan en `storage/app/public/thumbnails`
- Los exports se guardan en `storage/app/exports`

## 🔒 Seguridad

- Todas las rutas están protegidas con autenticación
- Usa Policies para autorización a nivel de modelo
- Las imágenes se validan antes de guardar
- Los inputs HTML se sanitizan

## 📚 Recursos

- [Laravel 11 Docs](https://laravel.com/docs/11.x)
- [Inertia.js Docs](https://inertiajs.com)
- [Vue 3 Docs](https://vuejs.org)
- [Tiptap Docs](https://tiptap.dev)
- [Chart.js Docs](https://www.chartjs.org)

## 📄 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

---

**¡Disfruta creando tu diario personal!** 📖✨
