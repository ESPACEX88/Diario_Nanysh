<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * Export user data to JSON.
     *
     * @param User $user
     * @return string Path to exported file
     */
    public function exportToJSON(User $user): string
    {
        $data = [
            'user' => $user->only(['name', 'email', 'created_at']),
            'diary_entries' => $user->diaryEntries()->with('tags', 'photos')->latest('date')->limit(2000)->get(),
            'notes' => $user->notes()->with('tags')->latest()->limit(1000)->get(),
            'photos' => $user->photos()->latest()->limit(1000)->get(),
            'albums' => $user->albums()->with(['photos' => function ($query) {
                $query->latest()->limit(50);
            }])->get(),
            'goals' => $user->goals()->get(),
            // Logs recientes por hábito (evita dump ilimitado)
            'habits' => $user->habits()->with(['habitLogs' => function ($query) {
                $query->where('completed_at', '>=', now()->subYear()->toDateString())
                    ->orderByDesc('completed_at');
            }])->get(),
            'gratitudes' => $user->gratitudes()->latest('date')->limit(1000)->get(),
            'settings' => $user->settings,
            'exported_at' => now()->toIso8601String(),
            'note' => 'Export limitado a volúmenes recientes para rendimiento en plan gratuito.',
        ];

        $filename = 'export_' . $user->id . '_' . now()->format('Y-m-d_His') . '.json';
        $path = 'exports/' . $filename;

        Storage::disk('local')->put($path, json_encode($data, JSON_PRETTY_PRINT));

        return $path;
    }

    /**
     * Export user data to PDF.
     *
     * @param User $user
     * @return string Path to exported file
     */
    public function exportToPDF(User $user): string
    {
        $data = [
            'user' => $user,
            'diary_entries' => $user->diaryEntries()->with('tags')->latest('date')->take(50)->get(),
            'notes' => $user->notes()->with('tags')->latest()->take(50)->get(),
            'goals' => $user->goals()->get(),
            'habits' => $user->habits()->get(),
            'gratitudes' => $user->gratitudes()->latest('date')->take(30)->get(),
        ];

        $pdf = Pdf::loadView('exports.diary', $data);
        $filename = 'export_' . $user->id . '_' . now()->format('Y-m-d_His') . '.pdf';
        $path = 'exports/' . $filename;

        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate a backup of all user data.
     *
     * @param User $user
     * @return string Path to backup file
     */
    public function generateBackup(User $user): string
    {
        return $this->exportToJSON($user);
    }
}
