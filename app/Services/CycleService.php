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
                'advice' => $this->getAdvice('menstrual', 1),
                'symptoms_summary' => [],
                'mood_trend' => [],
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

        $averageCycleLength = ! empty($cycleLengths)
            ? round(array_sum($cycleLengths) / count($cycleLengths))
            : 28;

        $today = now();
        $cycleInfo = $this->calculateCycleInfo($user, $today);

        $lastPeriod = $periods->last();
        $nextPeriod = $lastPeriod
            ? Carbon::parse($lastPeriod->date)->copy()->addDays((int) $averageCycleLength)->format('Y-m-d')
            : null;

        return [
            'average_cycle_length' => $averageCycleLength,
            'next_period_predicted' => $nextPeriod,
            'current_phase' => $cycleInfo['phase'],
            'cycle_day' => $cycleInfo['cycle_day'],
            'advice' => $this->getAdvice($cycleInfo['phase'], $cycleInfo['cycle_day']),
            'symptoms_summary' => $this->getSymptomsSummary($trackings),
            'mood_trend' => $this->getMoodTrend($trackings),
            'total_cycles' => count($cycleLengths) + 1,
        ];
    }
    
    /**
     * Obtener consejos según la fase del ciclo
     */
    private function getAdvice(string $phase, int $cycleDay): array
    {
        $advice = [
            'title' => '',
            'tips' => [],
            'wellness' => [],
        ];
        
        switch ($phase) {
            case 'menstrual':
                $advice['title'] = 'Fase Menstrual - Días de Descanso';
                $advice['tips'] = [
                    '💆 Descansa y escucha a tu cuerpo',
                    '🔥 Usa una bolsa de agua caliente para aliviar cólicos',
                    '💧 Mantente hidratada, bebe mucha agua',
                    '🥗 Come alimentos ricos en hierro (espinacas, lentejas)',
                    '🧘 Practica yoga suave o estiramientos',
                    '😴 Duerme lo suficiente (8-9 horas)',
                ];
                $advice['wellness'] = [
                    'Evita el ejercicio intenso',
                    'Reduce la cafeína si tienes cólicos',
                    'Date un baño caliente relajante',
                ];
                break;
                
            case 'follicular':
                $advice['title'] = 'Fase Folicular - Energía Renovada';
                $advice['tips'] = [
                    '💪 Es un buen momento para ejercicio intenso',
                    '🎯 Aprovecha tu energía para proyectos nuevos',
                    '🥗 Come alimentos ricos en proteínas',
                    '🧠 Tu concentración está en su punto máximo',
                    '💃 Es un buen momento para actividades sociales',
                    '📚 Aprovecha para aprender algo nuevo',
                ];
                $advice['wellness'] = [
                    'Aumenta tu actividad física gradualmente',
                    'Mantén una dieta balanceada',
                    'Aprovecha tu energía mental',
                ];
                break;
                
            case 'ovulation':
                $advice['title'] = 'Fase de Ovulación - Pico de Energía';
                $advice['tips'] = [
                    '🌟 Estás en tu mejor momento de energía',
                    '💪 Ideal para entrenamientos desafiantes',
                    '💬 Tu comunicación está en su mejor momento',
                    '🎨 Aprovecha para actividades creativas',
                    '💑 Es un buen momento para conexión social',
                    '🏃 Puedes hacer ejercicio de alta intensidad',
                ];
                $advice['wellness'] = [
                    'Aprovecha tu energía al máximo',
                    'Mantén una buena hidratación',
                    'Come alimentos nutritivos',
                ];
                break;
                
            case 'luteal':
                $advice['title'] = 'Fase Lútea - Preparación';
                $advice['tips'] = [
                    '🍫 Puedes tener antojos, elige opciones saludables',
                    '🧘 Practica técnicas de relajación',
                    '💤 Asegúrate de dormir bien',
                    '🥑 Come alimentos ricos en magnesio (aguacate, nueces)',
                    '📝 Lleva un diario de síntomas si es necesario',
                    '💧 Reduce la retención de líquidos bebiendo agua',
                ];
                $advice['wellness'] = [
                    'Evita el exceso de sal',
                    'Haz ejercicio moderado',
                    'Practica autocuidado',
                ];
                break;
        }
        
        return $advice;
    }
    
    /**
     * Resumen de síntomas más comunes
     */
    private function getSymptomsSummary($trackings): array
    {
        $symptomsCount = [];
        foreach ($trackings as $tracking) {
            if ($tracking->symptoms && is_array($tracking->symptoms)) {
                foreach ($tracking->symptoms as $symptom) {
                    $symptomsCount[$symptom] = ($symptomsCount[$symptom] ?? 0) + 1;
                }
            }
        }
        
        arsort($symptomsCount);
        return array_slice($symptomsCount, 0, 5, true);
    }
    
    /**
     * Tendencia de estado de ánimo
     */
    private function getMoodTrend($trackings): array
    {
        $moods = [];
        foreach ($trackings as $tracking) {
            if ($tracking->mood) {
                $moods[] = [
                    'date' => $tracking->date->format('Y-m-d'),
                    'mood' => $tracking->mood,
                    'phase' => $tracking->phase,
                ];
            }
        }
        
        return array_slice($moods, 0, 7); // Últimos 7 días
    }
}

