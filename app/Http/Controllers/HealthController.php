<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     */
    public function index()
    {
        $status = 'ok';
        $checks = [];
        
        // Check database
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'error';
            $status = 'error';
        }
        
        // Check cache
        try {
            Cache::put('health_check', 'ok', 10);
            $checks['cache'] = Cache::get('health_check') === 'ok' ? 'ok' : 'error';
        } catch (\Exception $e) {
            $checks['cache'] = 'error';
            $status = 'error';
        }
        
        // Check storage
        try {
            $checks['storage'] = is_writable(storage_path()) ? 'ok' : 'error';
            if ($checks['storage'] === 'error') {
                $status = 'error';
            }
        } catch (\Exception $e) {
            $checks['storage'] = 'error';
            $status = 'error';
        }
        
        return response()->json([
            'status' => $status,
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $status === 'ok' ? 200 : 503);
    }
}

