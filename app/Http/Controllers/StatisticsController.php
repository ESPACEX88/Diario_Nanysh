<?php

namespace App\Http\Controllers;

use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index()
    {
        $userId = Auth::id();
        
        // Cachear estadísticas por 5 minutos para mejorar rendimiento
        $cacheKey = "statistics_user_{$userId}";
        $stats = \Cache::remember($cacheKey, 300, function () use ($userId) {
            return [
                'totals' => $this->statisticsService->getTotalCounts($userId),
                'mood_distribution' => $this->statisticsService->getMoodDistribution($userId),
                'mood_trends' => $this->statisticsService->getMoodTrends($userId, 30),
                'word_cloud' => $this->statisticsService->getWordCloud($userId, 30),
                'monthly_report' => $this->statisticsService->getMonthlyReport($userId, now()->year, now()->month),
            ];
        });

        return Inertia::render('Statistics/Index', [
            'statistics' => $stats,
        ]);
    }

    public function moodTrends(Request $request)
    {
        $days = $request->get('days', 30);
        $userId = Auth::id();
        
        $trends = $this->statisticsService->getMoodTrends($userId, $days);
        
        return response()->json($trends);
    }
}
