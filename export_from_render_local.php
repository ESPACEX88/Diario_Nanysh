<?php

/**
 * Script para exportar datos de Render desde tu computadora local
 * 
 * USO:
 * 1. Asegúrate de tener la extensión pdo_pgsql habilitada en PHP
 * 2. Ejecuta: php export_from_render_local.php
 */

// ============================================
// CONFIGURACIÓN - DATOS DE RENDER
// ============================================

$renderDbConfig = [
    'host' => 'dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com',
    'port' => '5432',
    'database' => 'diario_fhd4',
    'username' => 'diario_fhd4_user',
    'password' => 'z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY',
];

$exportDir = __DIR__ . '/exports';

// ============================================
// NO EDITES NADA DE AQUÍ HACIA ABAJO
// ============================================

echo "🔄 Intentando conectar a Render y exportar datos...\n\n";

// Crear directorio de exportación
if (!is_dir($exportDir)) {
    mkdir($exportDir, 0755, true);
}

try {
    // Conectar a Render
    echo "📡 Conectando a Render...\n";
    $dsn = "pgsql:host={$renderDbConfig['host']};port={$renderDbConfig['port']};dbname={$renderDbConfig['database']}";
    
    $pdo = new PDO(
        $dsn,
        $renderDbConfig['username'],
        $renderDbConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ ¡Conectado exitosamente a Render!\n\n";

    // Obtener lista de tablas
    echo "📋 Obteniendo lista de tablas...\n";
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_type = 'BASE TABLE'
        ORDER BY table_name
    ");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📊 Encontradas " . count($tables) . " tablas:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    echo "\n";

    // Exportar cada tabla
    $totalRecords = 0;

    foreach ($tables as $table) {
        echo "📦 Exportando tabla: $table\n";
        
        try {
            $stmt = $pdo->query("SELECT * FROM \"$table\"");
            $rows = $stmt->fetchAll();
            
            $rowCount = count($rows);
            echo "   Registros encontrados: $rowCount\n";
            
            if ($rowCount === 0) {
                echo "   ⏭️  Tabla vacía, saltando...\n\n";
                continue;
            }

            // Convertir a arrays
            $data = array_map(function ($row) {
                return $row;
            }, $rows);

            // Guardar como JSON
            $filename = $exportDir . '/' . $table . '.json';
            file_put_contents(
                $filename,
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            echo "   ✅ Exportada a: $filename\n";
            $totalRecords += $rowCount;
            
        } catch (Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }

    // Resumen final
    echo "═══════════════════════════════════════\n";
    echo "📊 EXPORTACIÓN COMPLETADA\n";
    echo "═══════════════════════════════════════\n";
    echo "✅ Total de registros exportados: $totalRecords\n";
    echo "📁 Archivos guardados en: $exportDir\n\n";
    
    echo "📋 Archivos creados:\n";
    $files = glob($exportDir . '/*.json');
    foreach ($files as $file) {
        $size = filesize($file);
        $sizeFormatted = $this->formatBytes($size);
        echo "   - " . basename($file) . " ($sizeFormatted)\n";
    }
    
    echo "\n";
    echo "💡 Siguiente paso: Importa estos archivos a Neon usando:\n";
    echo "   php artisan db:import-json $exportDir\n\n";

} catch (PDOException $e) {
    echo "\n❌ ERROR: No se pudo conectar a Render\n";
    echo "   Mensaje: " . $e->getMessage() . "\n\n";
    
    echo "💡 Posibles causas:\n";
    echo "   1. La base de datos está completamente inaccesible\n";
    echo "   2. La extensión pdo_pgsql no está instalada en PHP\n";
    echo "   3. Problemas de red/firewall\n\n";
    
    echo "🔧 Soluciones:\n";
    echo "   1. Verifica que la extensión esté instalada:\n";
    echo "      php -m | grep pdo_pgsql\n";
    echo "   2. Si no está, instálala en Laragon:\n";
    echo "      - Abre Laragon\n";
    echo "      - Menu → PHP → Extensions → pdo_pgsql\n";
    echo "   3. Intenta usar DBeaver en su lugar\n";
    echo "   4. Contacta soporte de Render\n";
    
    exit(1);
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

