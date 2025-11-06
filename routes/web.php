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
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Diary Entries
    Route::resource('diary', DiaryEntryController::class);
    Route::post('diary/{diary}/favorite', [DiaryEntryController::class, 'toggleFavorite'])->name('diary.favorite');

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

    // Export
    Route::get('export', [ExportController::class, 'export'])->name('export');

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
});

require __DIR__.'/auth.php';
