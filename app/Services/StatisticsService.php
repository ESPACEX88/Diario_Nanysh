<?php

namespace App\Services;

use App\Models\DiaryEntry;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Note;
use App\Models\Photo;
use App\Models\Gratitude;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get total counts for all resources.
     *
     * @param int $userId
     * @return array
     */
    public function getTotalCounts(int $userId): array
    {
        return [
            'diary_entries' => DiaryEntry::where('user_id', $userId)->count(),
            'notes' => Note::where('user_id', $userId)->count(),
            'photos' => Photo::where('user_id', $userId)->count(),
            'goals' => Goal::where('user_id', $userId)->count(),
            'habits' => Habit::where('user_id', $userId)->where('is_active', true)->count(),
            'gratitudes' => Gratitude::where('user_id', $userId)->count(),
        ];
    }

    /**
     * Get mood distribution.
     *
     * @param int $userId
     * @return array
     */
    public function getMoodDistribution(int $userId): array
    {
        return DiaryEntry::where('user_id', $userId)
            ->select('mood', DB::raw('count(*) as count'))
            ->groupBy('mood')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'mood')
            ->toArray();
    }

    /**
     * Get mood trends over time.
     *
     * @param int $userId
     * @param int $days
     * @return array
     */
    public function getMoodTrends(int $userId, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return DiaryEntry::where('user_id', $userId)
            ->where('date', '>=', $startDate)
            ->select('date', 'mood')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('date')
            ->map(function ($entries) {
                return $entries->pluck('mood')->toArray();
            })
            ->toArray();
    }

    /**
     * Get most used words from diary entries.
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getWordCloud(int $userId, int $limit = 50): array
    {
        $entries = DiaryEntry::where('user_id', $userId)
            ->select('title', 'content')
            ->get();

        $text = $entries->pluck('title')->join(' ') . ' ' . $entries->pluck('content')->join(' ');
        $words = str_word_count(strtolower($text), 1);
        $wordCounts = array_count_values($words);

        // Remove common words
        $stopWords = ['el', 'la', 'de', 'que', 'y', 'a', 'en', 'un', 'es', 'se', 'no', 'te', 'lo', 'le', 'da', 'su', 'por', 'son', 'con', 'para', 'del', 'las', 'los', 'una', 'como', 'pero', 'sus', 'the', 'is', 'at', 'which', 'on', 'a', 'an', 'as', 'are', 'was', 'were', 'been', 'be', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'should', 'could', 'may', 'might', 'must', 'can'];
        
        foreach ($stopWords as $stopWord) {
            unset($wordCounts[$stopWord]);
        }

        arsort($wordCounts);
        return array_slice($wordCounts, 0, $limit, true);
    }

    /**
     * Get monthly report.
     *
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMonthlyReport(int $userId, int $year, int $month): array
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();

        return [
            'diary_entries' => DiaryEntry::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->count(),
            'notes' => Note::where('user_id', $userId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'photos' => Photo::where('user_id', $userId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'goals_completed' => Goal::where('user_id', $userId)
                ->where('is_completed', true)
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->count(),
            'habit_completions' => HabitLog::where('user_id', $userId)
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->count(),
            'gratitudes' => Gratitude::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->count(),
        ];
    }
}

