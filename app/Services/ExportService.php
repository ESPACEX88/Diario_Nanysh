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
            'diary_entries' => $user->diaryEntries()->with('tags', 'photos')->get(),
            'notes' => $user->notes()->with('tags')->get(),
            'photos' => $user->photos()->get(),
            'albums' => $user->albums()->with('photos')->get(),
            'goals' => $user->goals()->get(),
            'habits' => $user->habits()->with('habitLogs')->get(),
            'gratitudes' => $user->gratitudes()->get(),
            'settings' => $user->settings,
            'exported_at' => now()->toIso8601String(),
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
            'diary_entries' => $user->diaryEntries()->latest('date')->take(50)->get(),
            'notes' => $user->notes()->latest()->take(50)->get(),
            'goals' => $user->goals()->get(),
            'habits' => $user->habits()->with('habitLogs')->get(),
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

