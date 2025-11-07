<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

        return Inertia::render('MiniGame/Doors', [
            'pet' => $petData,
            'result' => $result,
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

        // Generar resultado aleatorio: 2 puertas ganan, 1 pierde
        $winningDoors = [];
        $losingDoor = rand(1, 3);
        
        // Las otras 2 puertas son ganadoras
        for ($i = 1; $i <= 3; $i++) {
            if ($i !== $losingDoor) {
                $winningDoors[] = $i;
            }
        }

        $selectedDoor = $request->door;
        $won = in_array($selectedDoor, $winningDoors);

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
                ])
                ->with('pet', $pet->fresh());
        } else {
            // Perder: no se gana nada pero tampoco se pierde
            return redirect()->route('minigame.doors')
                ->with('result', [
                    'won' => false,
                    'coins' => 0,
                    'message' => "¡Oh no! 😅 Esta puerta estaba vacía. ¡Intenta de nuevo!",
                    'winningDoors' => $winningDoors,
                    'losingDoor' => $losingDoor,
                ])
                ->with('pet', $pet->fresh());
        }
    }
}

