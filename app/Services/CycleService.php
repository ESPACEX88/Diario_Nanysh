<?php

namespace App\Services;

use App\Models\CycleTracking;
use App\Models\User;
use Carbon\Carbon;

class CycleService
{
    /**
     * Calcular la fase y día del ciclo basándose en registros anteriores
     */
    public function calculateCycleInfo(User $user, Carbon $date): array
    {
        // Buscar el último inicio de período (menstrual con flow_level > 0)
        $lastPeriod = CycleTracking::where('user_id', $user->id)
            ->where('phase', 'menstrual')
            ->whereNotNull('flow_level')
            ->where('flow_level', '>', 0)
            ->orderBy('date', 'desc')
            ->first();
        
        if (!$lastPeriod) {
            // Si no hay registros previos, asumir que hoy es día 1 del ciclo
            return [
                'phase' => 'menstrual',
                'cycle_day' => 1,
            ];
        }
        
        $lastPeriodDate = Carbon::parse($lastPeriod->date);
        $daysSincePeriod = $lastPeriodDate->diffInDays($date, false);
        
        // Si la fecha es anterior al último período, asumir que es un nuevo ciclo
        if ($daysSincePeriod < 0) {
            return [
                'phase' => 'menstrual',
                'cycle_day' => 1,
            ];
        }
        
        // Calcular día del ciclo (asumiendo ciclo de 28 días promedio)
        $cycleDay = ($daysSincePeriod % 28) + 1;
        if ($cycleDay > 28) {
            $cycleDay = 1;
        }
        
        // Determinar fase basándose en el día del ciclo
        $phase = $this->determinePhase($cycleDay);
        
        return [
            'phase' => $phase,
            'cycle_day' => $cycleDay,
        ];
    }
    
    /**
     * Determinar la fase del ciclo basándose en el día
     */
    private function determinePhase(int $cycleDay): string
    {
        // Menstrual: días 1-5
        if ($cycleDay >= 1 && $cycleDay <= 5) {
            return 'menstrual';
        }
        
        // Folicular: días 6-13
        if ($cycleDay >= 6 && $cycleDay <= 13) {
            return 'follicular';
        }
        
        // Ovulación: días 14-16
        if ($cycleDay >= 14 && $cycleDay <= 16) {
            return 'ovulation';
        }
        
        // Lútea: días 17-28
        return 'luteal';
    }
    
    /**
     * Predecir el próximo período
     */
    public function predictNextPeriod(User $user): ?Carbon
    {
        $lastPeriod = CycleTracking::where('user_id', $user->id)
            ->where('phase', 'menstrual')
            ->whereNotNull('flow_level')
            ->where('flow_level', '>', 0)
            ->orderBy('date', 'desc')
            ->first();
        
        if (!$lastPeriod) {
            return null;
        }
        
        $lastPeriodDate = Carbon::parse($lastPeriod->date);
        // Asumir ciclo de 28 días
        return $lastPeriodDate->copy()->addDays(28);
    }
    
    /**
     * Obtener estadísticas del ciclo
     */
    public function getCycleStats(User $user): array
    {
        $trackings = CycleTracking::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(90)
            ->get();
        
        if ($trackings->isEmpty()) {
            return [
                'average_cycle_length' => 28,
                'next_period_predicted' => null,
                'current_phase' => null,
                'cycle_day' => null,
            ];
        }
        
        // Calcular longitud promedio del ciclo
        $periods = $trackings->where('phase', 'menstrual')
            ->where('flow_level', '>', 0)
            ->sortBy('date')
            ->values();
        
        $cycleLengths = [];
        for ($i = 0; $i < $periods->count() - 1; $i++) {
            $current = Carbon::parse($periods[$i]->date);
            $next = Carbon::parse($periods[$i + 1]->date);
            $cycleLengths[] = abs($current->diffInDays($next, false));
        }
        
        $averageCycleLength = !empty($cycleLengths) 
            ? round(array_sum($cycleLengths) / count($cycleLengths))
            : 28;
        
        // Información del ciclo actual
        $today = now();
        $cycleInfo = $this->calculateCycleInfo($user, $today);
        
        return [
            'average_cycle_length' => $averageCycleLength,
            'next_period_predicted' => $this->predictNextPeriod($user)?->format('Y-m-d'),
            'current_phase' => $cycleInfo['phase'],
            'cycle_day' => $cycleInfo['cycle_day'],
        ];
    }
}

