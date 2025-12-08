<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ExportDatabaseController extends Controller
{
    /**
     * Exportar todos los datos de la base de datos a JSON
     * 
     * Acceso: GET /api/export-database
     */
    public function export()
    {
        try {
            $tables = $this->getTables();
            $exportData = [];

            foreach ($tables as $table) {
                $data = DB::table($table)->get()->toArray();
                
                // Convertir a arrays
                $exportData[$table] = array_map(function ($item) {
                    return (array) $item;
                }, $data);
            }

            // Crear respuesta JSON
            $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            // Retornar como descarga
            return Response::make($json, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="database_export_' . date('Y-m-d_His') . '.json"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al exportar datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar una tabla específica
     * 
     * Acceso: GET /api/export-database/{table}
     */
    public function exportTable($table)
    {
        try {
            // Verificar que la tabla existe
            $tables = $this->getTables();
            if (!in_array($table, $tables)) {
                return response()->json([
                    'error' => 'Tabla no encontrada'
                ], 404);
            }

            $data = DB::table($table)->get()->toArray();
            
            $exportData = array_map(function ($item) {
                return (array) $item;
            }, $data);

            $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            return Response::make($json, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $table . '_export_' . date('Y-m-d_His') . '.json"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al exportar tabla: ' . $e->getMessage()
            ], 500);
        }
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
}

