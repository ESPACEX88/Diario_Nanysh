<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportDatabaseFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import-json {directory? : Directorio con archivos JSON a importar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa datos desde archivos JSON exportados previamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $importDir = $this->argument('directory') ?: storage_path('app/exports');
        
        if (!File::exists($importDir)) {
            $this->error("❌ El directorio no existe: {$importDir}");
            return 1;
        }

        $this->info('🔄 Iniciando importación de datos desde JSON...');
        $this->newLine();

        // Obtener archivos JSON
        $jsonFiles = File::glob($importDir . '/*.json');
        
        if (empty($jsonFiles)) {
            $this->error("❌ No se encontraron archivos JSON en: {$importDir}");
            return 1;
        }

        $this->info("📊 Encontrados " . count($jsonFiles) . " archivos JSON");
        $this->newLine();

        $totalImported = 0;
        $totalErrors = 0;

        foreach ($jsonFiles as $file) {
            $tableName = File::name($file);
            $this->info("📦 Importando tabla: {$tableName}");
            
            try {
                $jsonContent = File::get($file);
                $data = json_decode($jsonContent, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("   ❌ Error al decodificar JSON: " . json_last_error_msg());
                    $totalErrors++;
                    continue;
                }
                
                if (empty($data)) {
                    $this->warn("   ⏭️  Archivo vacío, saltando...");
                    $this->newLine();
                    continue;
                }

                $this->line("   Registros a importar: " . count($data));

                // Verificar si la tabla existe
                $tableExists = DB::select("
                    SELECT EXISTS (
                        SELECT FROM information_schema.tables 
                        WHERE table_schema = 'public' 
                        AND table_name = ?
                    )
                ", [$tableName]);

                if (!$tableExists[0]->exists) {
                    $this->warn("   ⚠️  Tabla no existe. Asegúrate de ejecutar las migraciones primero.");
                    $this->newLine();
                    continue;
                }

                // Insertar datos
                $inserted = 0;
                $skipped = 0;
                
                DB::beginTransaction();
                
                foreach ($data as $row) {
                    try {
                        // Intentar insertar
                        DB::table($tableName)->insert($row);
                        $inserted++;
                    } catch (\Exception $e) {
                        // Si es error de duplicado, lo ignoramos
                        if (strpos($e->getMessage(), 'duplicate') !== false || 
                            strpos($e->getMessage(), 'unique') !== false) {
                            $skipped++;
                        } else {
                            throw $e;
                        }
                    }
                }
                
                DB::commit();
                
                $this->info("   ✅ Insertados: {$inserted} registros");
                if ($skipped > 0) {
                    $this->line("   ⏭️  Omitidos (duplicados): {$skipped} registros");
                }
                $totalImported += $inserted;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("   ❌ Error: " . $e->getMessage());
                $totalErrors++;
            }
            
            $this->newLine();
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info('📊 IMPORTACIÓN COMPLETADA');
        $this->info('═══════════════════════════════════════');
        $this->info("✅ Registros importados: {$totalImported}");
        if ($totalErrors > 0) {
            $this->error("❌ Errores: {$totalErrors}");
        }
        $this->newLine();

        return 0;
    }
}

