<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function index()
    {
        return \Inertia\Inertia::render('Export/Index');
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'json');

        try {
            switch ($format) {
                case 'json':
                    $path = $this->exportService->exportToJSON($user);
                    return $this->downloadFile($path, 'json');
                    
                case 'csv':
                    $path = $this->exportToCSV($user);
                    return $this->downloadFile($path, 'csv');
                    
                case 'pdf':
                    $path = $this->exportService->exportToPDF($user);
                    return $this->downloadFile($path, 'pdf');
                    
                default:
                    return back()->with('error', 'Formato no válido');
            }
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Error al exportar datos: ' . $e->getMessage());
        }
    }

    private function exportToCSV($user): string
    {
        $entries = $user->diaryEntries()->with('tags')->get();
        
        $filename = 'export_' . $user->id . '_' . now()->format('Y-m-d_His') . '.csv';
        $path = 'exports/' . $filename;
        
        // Asegurar que el directorio existe
        Storage::disk('local')->makeDirectory('exports');
        
        $filePath = Storage::disk('local')->path($path);
        $file = fopen($filePath, 'w');
        
        if ($file === false) {
            throw new \Exception('No se pudo crear el archivo CSV');
        }
        
        // Headers con BOM para UTF-8
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($file, ['Fecha', 'Título', 'Contenido', 'Estado de Ánimo', 'Favorito', 'Etiquetas'], ';');
        
        // Data
        foreach ($entries as $entry) {
            $tags = $entry->tags->pluck('name')->join(', ');
            fputcsv($file, [
                $entry->date->format('Y-m-d'),
                $entry->title ?? '',
                strip_tags($entry->content ?? ''),
                $entry->mood ?? '',
                $entry->is_favorite ? 'Sí' : 'No',
                $tags,
            ], ';');
        }
        
        fclose($file);
        
        return $path;
    }

    private function downloadFile(string $path, string $format): StreamedResponse
    {
        $filename = basename($path);
        $mimeTypes = [
            'json' => 'application/json',
            'csv' => 'text/csv',
            'pdf' => 'application/pdf',
        ];

        return Storage::disk('local')->download($path, $filename, [
            'Content-Type' => $mimeTypes[$format] ?? 'application/octet-stream',
        ]);
    }
}
