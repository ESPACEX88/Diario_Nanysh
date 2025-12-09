<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--format=json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $format = $this->option('format');
        $timestamp = Carbon::now()->format('Y-m-d_His');
        
        try {
            if ($format === 'json') {
                $this->backupToJson($timestamp);
            } else {
                $this->error('Formato no soportado. Usa: json');
                return 1;
            }
            
            $this->info("✅ Backup creado exitosamente: backup_{$timestamp}.{$format}");
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Error al crear backup: " . $e->getMessage());
            return 1;
        }
    }
    
    private function backupToJson(string $timestamp)
    {
        $backup = [
            'timestamp' => Carbon::now()->toIso8601String(),
            'tables' => [],
        ];
        
        // Obtener todas las tablas
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        
        foreach ($tables as $table) {
            $tableName = $table->table_name;
            $data = DB::table($tableName)->get()->toArray();
            $backup['tables'][$tableName] = $data;
        }
        
        $filename = "backup_{$timestamp}.json";
        Storage::disk('local')->put("backups/{$filename}", json_encode($backup, JSON_PRETTY_PRINT));
    }
}

