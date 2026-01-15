<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryEntryController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\GratitudeController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\WorkoutLogController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Keep-alive endpoint público (sin autenticación)
Route::get('/keep-alive', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Servicio activo',
        'timestamp' => now()->toDateTimeString(),
    ]);
})->name('keep-alive');

// Health check endpoint (sin autenticación)
Route::get('/health', [\App\Http\Controllers\HealthController::class, 'index'])->name('health');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Diary Entries
    Route::resource('diary', DiaryEntryController::class);
    Route::post('diary/{diary}/favorite', [DiaryEntryController::class, 'toggleFavorite'])->name('diary.favorite');

    // Tags
    Route::get('tags', [\App\Http\Controllers\TagController::class, 'index'])->name('tags.index');
    Route::get('tags/user', [\App\Http\Controllers\TagController::class, 'userTags'])->name('tags.user');
    Route::post('tags', [\App\Http\Controllers\TagController::class, 'store'])->name('tags.store');
    Route::put('tags/{tag}', [\App\Http\Controllers\TagController::class, 'update'])->name('tags.update');
    Route::delete('tags/{tag}', [\App\Http\Controllers\TagController::class, 'destroy'])->name('tags.destroy');

    // Notes
    Route::resource('notes', NoteController::class);
    Route::post('notes/reorder', [NoteController::class, 'reorder'])->name('notes.reorder');

    // Albums
    Route::resource('albums', AlbumController::class);

    // Photos
    Route::resource('photos', PhotoController::class);

    // Goals
    Route::resource('goals', GoalController::class);

    // Habits
    Route::resource('habits', HabitController::class);
    Route::post('habits/{habit}/log', [HabitLogController::class, 'toggle'])->name('habits.log');

    // Gratitude
    Route::resource('gratitude', GratitudeController::class);

    // Statistics
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('statistics/mood-trends', [StatisticsController::class, 'moodTrends'])->name('statistics.mood-trends');

    // Export (con rate limiting)
    Route::get('export', [ExportController::class, 'index'])->name('export.index');
    Route::get('export/download', [ExportController::class, 'export'])
        ->middleware('throttle.sensitive')
        ->name('export');

    // Settings
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pet (Snoopy)
    Route::get('/pet', [PetController::class, 'index'])->name('pet.index');
    Route::post('/pet/feed', [PetController::class, 'feed'])->name('pet.feed');
    Route::post('/pet/play', [PetController::class, 'play'])->name('pet.play');
    Route::post('/pet/care', [PetController::class, 'care'])->name('pet.care');
    Route::post('/pet/rename', [PetController::class, 'rename'])->name('pet.rename');

    // Todos (Tareas)
    Route::resource('todos', \App\Http\Controllers\TodoController::class);
    Route::post('todos/{todo}/toggle', [\App\Http\Controllers\TodoController::class, 'toggleComplete'])->name('todos.toggle');

    // Events (Eventos)
    Route::resource('events', \App\Http\Controllers\EventController::class);

    // Wishlist (Lista de deseos)
    Route::resource('wishlist', \App\Http\Controllers\WishlistItemController::class);

    // Achievements (Logros)
    Route::get('achievements', [\App\Http\Controllers\AchievementController::class, 'index'])->name('achievements.index');

    // Day Counters (Contadores de días)
    Route::resource('counters', \App\Http\Controllers\DayCounterController::class);

    // Dreams (Sueños)
    Route::resource('dreams', \App\Http\Controllers\DreamController::class);

    // Media Items (Libros/Películas)
    Route::resource('media', \App\Http\Controllers\MediaItemController::class);

    // Cycle Tracking (Seguimiento de ciclo)
    Route::resource('cycle', \App\Http\Controllers\CycleTrackingController::class);

    // Favorite Meals (Comidas favoritas)
    Route::resource('meals', \App\Http\Controllers\FavoriteMealController::class);

    // Recipe Catalog (Recetario)
    Route::get('recipes', [\App\Http\Controllers\RecipeCatalogController::class, 'index'])->name('recipes.index');
    Route::get('recipes/{id}', [\App\Http\Controllers\RecipeCatalogController::class, 'show'])->name('recipes.show');

    // Motivational Quotes (Frases motivacionales)
    Route::get('quote/daily', [\App\Http\Controllers\MotivationalQuoteController::class, 'daily'])->name('quote.daily');

    // Workout Logs (Registro de entrenamientos)
    Route::get('workouts/calendar/data', [WorkoutLogController::class, 'calendar'])->name('workouts.calendar');
    Route::resource('workouts', WorkoutLogController::class);

    // Mini Games (Minijuegos)
    Route::get('minigame/doors', [\App\Http\Controllers\MiniGameController::class, 'index'])->name('minigame.doors');
    Route::post('minigame/doors/play', [\App\Http\Controllers\MiniGameController::class, 'play'])->name('minigame.doors.play');
});

require __DIR__.'/auth.php';
