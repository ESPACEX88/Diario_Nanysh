<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;

class MiniGameController extends Controller
{
    /**
     * Mostrar el minijuego de puertas misteriosas
     */
    public function index()
    {
        $user = Auth::user();
        $pet = Pet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Snoopy',
                'level' => 1,
                'experience' => 0,
                'happiness' => 100,
                'hunger' => 100,
                'energy' => 100,
                'health' => 100,
                'coins' => 0,
            ]
        );

        $result = session('result');
        $petData = session('pet', $pet);

        // Calcular tiempo restante para poder jugar
        $canPlay = true;
        $nextPlayAt = null;
        $hoursRemaining = 0;
        $minutesRemaining = 0;

        if ($pet->last_minigame_at) {
            $nextPlayAt = $pet->last_minigame_at->copy()->addHours(12);
            $now = Carbon::now();
            
            if ($now->lt($nextPlayAt)) {
                $canPlay = false;
                $diff = $now->diff($nextPlayAt);
                $hoursRemaining = $diff->h;
                $minutesRemaining = $diff->i;
            }
        }

        return Inertia::render('MiniGame/Doors', [
            'pet' => $petData,
            'result' => $result,
            'canPlay' => $canPlay,
            'nextPlayAt' => $nextPlayAt ? $nextPlayAt->toDateTimeString() : null,
            'hoursRemaining' => $hoursRemaining,
            'minutesRemaining' => $minutesRemaining,
        ]);
    }

    /**
     * Jugar el minijuego de puertas misteriosas
     */
    public function play(Request $request)
    {
        $request->validate([
            'door' => 'required|integer|min:1|max:3',
        ]);

        $user = Auth::user();
        $pet = Pet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Snoopy',
                'level' => 1,
                'experience' => 0,
                'happiness' => 100,
                'hunger' => 100,
                'energy' => 100,
                'health' => 100,
                'coins' => 0,
            ]
        );

        // Verificar cooldown de 12 horas
        if ($pet->last_minigame_at) {
            $nextPlayAt = $pet->last_minigame_at->copy()->addHours(12);
            $now = Carbon::now();
            
            if ($now->lt($nextPlayAt)) {
                $diff = $now->diff($nextPlayAt);
                $hoursRemaining = $diff->h;
                $minutesRemaining = $diff->i;
                
                return redirect()->route('minigame.doors')
                    ->with('error', "Debes esperar {$hoursRemaining} horas y {$minutesRemaining} minutos para jugar de nuevo.");
            }
        }

        // Generar resultado aleatorio: 2 puertas ganan, 1 pierde
        // Primero elegimos qué puerta pierde
        $losingDoor = rand(1, 3);
        
        // Las otras 2 puertas son ganadoras
        $winningDoors = [];
        for ($i = 1; $i <= 3; $i++) {
            if ($i !== $losingDoor) {
                $winningDoors[] = $i;
            }
        }

        // Verificar que solo hay 2 puertas ganadoras
        if (count($winningDoors) !== 2) {
            // Si algo salió mal, regenerar
            $losingDoor = rand(1, 3);
            $winningDoors = [];
            for ($i = 1; $i <= 3; $i++) {
                if ($i !== $losingDoor) {
                    $winningDoors[] = $i;
                }
            }
        }

        $selectedDoor = (int) $request->door;
        $won = in_array($selectedDoor, $winningDoors);

        // Actualizar última vez que jugó
        $pet->last_minigame_at = Carbon::now();

        if ($won) {
            // Ganar moneditas aleatorias entre 10 y 50
            $coinsWon = rand(10, 50);
            $pet->coins += $coinsWon;
            $pet->happiness = min(100, $pet->happiness + 5);
            $pet->save();

            return redirect()->route('minigame.doors')
                ->with('result', [
                    'won' => true,
                    'coins' => $coinsWon,
                    'message' => "¡Felicidades! 🎉 Ganaste {$coinsWon} fichitas. ¡Snoopy está muy feliz!",
                    'winningDoors' => $winningDoors,
                    'losingDoor' => $losingDoor,
                    'selectedDoor' => $selectedDoor,
                ])
                ->with('pet', $pet->fresh());
        } else {
            // Perder: no se gana nada pero tampoco se pierde
            $pet->save();

            return redirect()->route('minigame.doors')
                ->with('result', [
                    'won' => false,
                    'coins' => 0,
                    'message' => "¡Oh no! 😅 Esta puerta estaba vacía. ¡Intenta de nuevo en 12 horas!",
                    'winningDoors' => $winningDoors,
                    'losingDoor' => $losingDoor,
                    'selectedDoor' => $selectedDoor,
                ])
                ->with('pet', $pet->fresh());
        }
    }
}

