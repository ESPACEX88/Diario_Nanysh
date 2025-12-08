<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ExportDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export {--format=json : Formato de exportación (json o sql)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporta todos los datos de la base de datos a archivos JSON o SQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $format = $this->option('format');
        $exportDir = storage_path('app/exports');
        
        // Crear directorio de exportación
        if (!File::exists($exportDir)) {
            File::makeDirectory($exportDir, 0755, true);
        }

        $this->info('🔄 Iniciando exportación de base de datos...');
        $this->newLine();

        try {
            // Obtener todas las tablas
            $tables = $this->getTables();
            $this->info("📊 Encontradas " . count($tables) . " tablas");
            $this->newLine();

            $totalRecords = 0;

            foreach ($tables as $table) {
                $this->info("📦 Exportando tabla: {$table}");
                
                $recordCount = DB::table($table)->count();
                $this->line("   Registros encontrados: {$recordCount}");
                
                if ($recordCount === 0) {
                    $this->warn("   ⏭️  Tabla vacía, saltando...");
                    $this->newLine();
                    continue;
                }

                if ($format === 'json') {
                    $this->exportTableToJson($table, $exportDir);
                } else {
                    $this->exportTableToSql($table, $exportDir);
                }
                
                $totalRecords += $recordCount;
                $this->info("   ✅ Exportada correctamente");
                $this->newLine();
            }

            $this->newLine();
            $this->info('═══════════════════════════════════════');
            $this->info('📊 EXPORTACIÓN COMPLETADA');
            $this->info('═══════════════════════════════════════');
            $this->info("✅ Total de registros exportados: {$totalRecords}");
            $this->info("📁 Archivos guardados en: {$exportDir}");
            $this->newLine();
            
            $this->info('📋 Archivos creados:');
            $files = File::files($exportDir);
            foreach ($files as $file) {
                $size = $this->formatBytes($file->getSize());
                $this->line("   - {$file->getFilename()} ({$size})");
            }
            
            $this->newLine();
            $this->warn('💡 Para descargar desde Render Shell:');
            $this->line('   1. Usa: cat ' . $exportDir . '/nombre_archivo.json');
            $this->line('   2. Copia el contenido y pégalo en un archivo local');
            $this->line('   3. O usa el comando: php artisan db:download-export');
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la exportación: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    /**
     * Obtener lista de tablas
     */
    private function getTables(): array
    {
        $tables = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ");

        return array_column($tables, 'table_name');
    }

    /**
     * Exportar tabla a JSON
     */
    private function exportTableToJson(string $table, string $exportDir): void
    {
        $data = DB::table($table)->get()->toArray();
        
        // Convertir objetos stdClass a arrays
        $dataArray = array_map(function ($item) {
            return (array) $item;
        }, $data);

        $filename = $exportDir . '/' . $table . '.json';
        File::put($filename, json_encode($dataArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Exportar tabla a SQL
     */
    private function exportTableToSql(string $table, string $exportDir): void
    {
        $data = DB::table($table)->get();
        
        $sql = "-- Exportación de tabla: {$table}\n";
        $sql .= "-- Fecha: " . now() . "\n\n";
        
        foreach ($data as $row) {
            $columns = array_keys((array) $row);
            $values = array_values((array) $row);
            
            // Escapar valores
            $escapedValues = array_map(function ($value) {
                if ($value === null) {
                    return 'NULL';
                }
                if (is_bool($value)) {
                    return $value ? 'true' : 'false';
                }
                if (is_numeric($value)) {
                    return $value;
                }
                return "'" . addslashes($value) . "'";
            }, $values);
            
            $columnList = '"' . implode('", "', $columns) . '"';
            $valueList = implode(', ', $escapedValues);
            
            $sql .= "INSERT INTO \"{$table}\" ({$columnList}) VALUES ({$valueList});\n";
        }
        
        $filename = $exportDir . '/' . $table . '.sql';
        File::put($filename, $sql);
    }

    /**
     * Formatear bytes a formato legible
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

