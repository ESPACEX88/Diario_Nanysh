<?php

/**
 * Script para migrar datos de la base de datos antigua de Render a Neon
 * 
 * USO:
 * 1. Configura las variables de conexión abajo
 * 2. Ejecuta: php migrate_to_neon.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ============================================
// CONFIGURACIÓN - EDITA ESTOS VALORES
// ============================================

// Base de datos ANTIGUA (Render)
$oldDbConfig = [
    'host' => 'dpg-d46flmqli9vc73fdn76g-a.virginia-postgres.render.com',
    'port' => '5432',
    'database' => 'diario_fhd4',
    'username' => 'diario_fhd4_user',
    'password' => 'z3VvHVixd3GecvgtnrVZ7m7CQl6u6WGY',
];

// Base de datos NUEVA (Neon) - Obtén estos datos de tu panel de Neon
$newDbConfig = [
    'host' => 'TU_HOST_NEON', // ej: ep-xxx-xxx.us-east-2.aws.neon.tech
    'port' => '5432',
    'database' => 'TU_DATABASE_NEON', // ej: neondb
    'username' => 'TU_USERNAME_NEON',
    'password' => 'TU_PASSWORD_NEON',
];

// ============================================
// NO EDITES NADA DE AQUÍ HACIA ABAJO
// ============================================

echo "🔄 Iniciando migración de datos de Render a Neon...\n\n";

try {
    // Conectar a base de datos antigua
    echo "📡 Conectando a base de datos antigua (Render)...\n";
    $oldDb = new PDO(
        "pgsql:host={$oldDbConfig['host']};port={$oldDbConfig['port']};dbname={$oldDbConfig['database']}",
        $oldDbConfig['username'],
        $oldDbConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    echo "✅ Conectado a Render\n\n";

    // Conectar a base de datos nueva
    echo "📡 Conectando a base de datos nueva (Neon)...\n";
    $newDb = new PDO(
        "pgsql:host={$newDbConfig['host']};port={$newDbConfig['port']};dbname={$newDbConfig['database']}",
        $newDbConfig['username'],
        $newDbConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    echo "✅ Conectado a Neon\n\n";

    // Obtener lista de tablas de la base antigua
    echo "📋 Obteniendo lista de tablas...\n";
    $tablesQuery = $oldDb->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_type = 'BASE TABLE'
        ORDER BY table_name
    ");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📊 Encontradas " . count($tables) . " tablas:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    echo "\n";

    // Migrar cada tabla
    $totalMigrated = 0;
    $totalErrors = 0;

    foreach ($tables as $table) {
        echo "🔄 Migrando tabla: $table\n";
        
        try {
            // Obtener datos de la tabla antigua
            $dataQuery = $oldDb->query("SELECT * FROM \"$table\"");
            $rows = $dataQuery->fetchAll();
            
            $rowCount = count($rows);
            echo "   📦 Encontrados $rowCount registros\n";
            
            if ($rowCount === 0) {
                echo "   ⏭️  Tabla vacía, saltando...\n\n";
                continue;
            }

            // Obtener columnas de la tabla
            $columnsQuery = $oldDb->query("
                SELECT column_name, data_type 
                FROM information_schema.columns 
                WHERE table_name = '$table' 
                AND table_schema = 'public'
                ORDER BY ordinal_position
            ");
            $columns = $columnsQuery->fetchAll();
            $columnNames = array_column($columns, 'column_name');

            // Verificar si la tabla existe en la nueva base de datos
            $tableExistsQuery = $newDb->query("
                SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_name = '$table'
                )
            ");
            $tableExists = $tableExistsQuery->fetchColumn();

            if (!$tableExists) {
                echo "   ⚠️  Tabla no existe en Neon. Asegúrate de ejecutar las migraciones primero.\n";
                echo "   💡 Ejecuta: php artisan migrate\n\n";
                continue;
            }

            // Preparar statement de inserción
            $placeholders = implode(', ', array_fill(0, count($columnNames), '?'));
            $columnList = '"' . implode('", "', $columnNames) . '"';
            $insertSql = "INSERT INTO \"$table\" ($columnList) VALUES ($placeholders)";
            
            // Para evitar duplicados, usamos ON CONFLICT (si hay primary key)
            // Primero intentamos obtener la primary key
            $pkQuery = $newDb->query("
                SELECT a.attname
                FROM pg_index i
                JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
                WHERE i.indrelid = '\"$table\"'::regclass
                AND i.indisprimary
                LIMIT 1
            ");
            $primaryKey = $pkQuery->fetchColumn();

            if ($primaryKey) {
                // Si hay primary key, usamos ON CONFLICT DO NOTHING
                $insertSql .= " ON CONFLICT DO NOTHING";
                echo "   🔑 Primary key detectada: $primaryKey (evitando duplicados)\n";
            }

            $insertStmt = $newDb->prepare($insertSql);

            // Insertar datos
            $inserted = 0;
            $skipped = 0;
            
            $newDb->beginTransaction();
            
            foreach ($rows as $row) {
                try {
                    $values = array_values($row);
                    $insertStmt->execute($values);
                    $inserted++;
                } catch (PDOException $e) {
                    // Si es error de duplicado, lo ignoramos
                    if (strpos($e->getMessage(), 'duplicate') !== false || 
                        strpos($e->getMessage(), 'unique') !== false) {
                        $skipped++;
                    } else {
                        throw $e;
                    }
                }
            }
            
            $newDb->commit();
            
            echo "   ✅ Insertados: $inserted registros\n";
            if ($skipped > 0) {
                echo "   ⏭️  Omitidos (duplicados): $skipped registros\n";
            }
            $totalMigrated += $inserted;
            
        } catch (Exception $e) {
            echo "   ❌ Error migrando $table: " . $e->getMessage() . "\n";
            $totalErrors++;
            if ($newDb->inTransaction()) {
                $newDb->rollBack();
            }
        }
        
        echo "\n";
    }

    // Resumen final
    echo "═══════════════════════════════════════\n";
    echo "📊 RESUMEN DE MIGRACIÓN\n";
    echo "═══════════════════════════════════════\n";
    echo "✅ Registros migrados: $totalMigrated\n";
    if ($totalErrors > 0) {
        echo "❌ Errores: $totalErrors\n";
    }
    echo "✅ Migración completada!\n\n";
    
    echo "🔍 Verifica los datos en Neon:\n";
    echo "   1. Conecta a Neon con DBeaver\n";
    echo "   2. Revisa que las tablas tengan datos\n";
    echo "   3. Prueba tu aplicación en Render\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR CRÍTICO: " . $e->getMessage() . "\n";
    echo "\n💡 Verifica:\n";
    echo "   - Que las credenciales sean correctas\n";
    echo "   - Que ambas bases de datos estén accesibles\n";
    echo "   - Que tengas las extensiones de PostgreSQL instaladas\n";
    exit(1);
}

