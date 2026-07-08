<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesAchievements;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class PetController extends Controller
{
    use HandlesAchievements;

    /**
     * Display the pet.
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
                'coins' => 50, // Starting coins
            ]
        );

        // Update stats based on time
        $pet->decreaseStats();
        $pet->save();

        // Refresh pet to get latest data
        $pet->refresh();

        return Inertia::render('Pet/Index', [
            'pet' => [
                'id' => $pet->id,
                'name' => $pet->name,
                'level' => (int) $pet->level,
                'experience' => (int) $pet->experience,
                'happiness' => (int) $pet->happiness,
                'hunger' => (int) $pet->hunger,
                'energy' => (int) $pet->energy,
                'health' => (int) $pet->health,
                'coins' => (int) $pet->coins,
                'mood' => $pet->mood,
            ],
        ]);
    }

    /**
     * Feed the pet.
     */
    public function feed()
    {
        $pet = Pet::where('user_id', Auth::id())->firstOrFail();
        
        // Cost to feed: 10 coins - Check FIRST
        $feedCost = 10;
        
        if ($pet->coins < $feedCost) {
            return redirect()->route('pet.index')->with('error', "No tienes suficientes fichitas. Necesitas {$feedCost} fichitas para alimentar a Snoopy. ¡Escribe en tu diario con estados de ánimo felices para ganar más! 💰");
        }
        
        // Check if can feed (cooldown of 30 minutes)
        if ($pet->last_fed_at && $pet->last_fed_at->diffInMinutes(now()) < 30) {
            return redirect()->route('pet.index')->with('error', 'Snoopy no tiene hambre todavía. Espera un poco más.');
        }

        $pet->coins = max(0, $pet->coins - $feedCost);
        $pet->hunger = min(100, $pet->hunger + 30);
        $pet->happiness = min(100, $pet->happiness + 10);
        $pet->last_fed_at = now();
        
        $leveledUp = $pet->addExperience(20);
        $pet->save();
        $pet->refresh();

        $message = $leveledUp 
            ? "¡Snoopy está muy feliz! ¡Subió de nivel! 🎉 (Costó {$feedCost} fichitas)" 
            : "¡Snoopy ha comido y está más feliz! 🍽️ (Costó {$feedCost} fichitas). Tienes {$pet->coins} fichitas restantes.";

        $unlocked = $this->syncAchievements(['pet']);

        return redirect()->route('pet.index')->with('success', $message . $this->achievementMessage($unlocked));
    }

    /**
     * Play with the pet.
     */
    public function play()
    {
        $pet = Pet::where('user_id', Auth::id())->firstOrFail();
        
        // Cost to play: 15 coins - Check FIRST
        $playCost = 15;
        
        if ($pet->coins < $playCost) {
            return redirect()->route('pet.index')->with('error', "No tienes suficientes fichitas. Necesitas {$playCost} fichitas para jugar con Snoopy. ¡Escribe en tu diario con estados de ánimo felices para ganar más! 💰");
        }

        // No cooldown - can play unlimited times as long as you have coins!
        // Just check if Snoopy has enough energy
        if ($pet->energy < 20) {
            return redirect()->route('pet.index')->with('error', 'Snoopy está muy cansado. Necesita más energía.');
        }

        $pet->coins = max(0, $pet->coins - $playCost);
        $pet->happiness = min(100, $pet->happiness + 25);
        $pet->energy = max(0, $pet->energy - 15);
        $pet->last_played_at = now();
        
        $leveledUp = $pet->addExperience(30);
        $pet->save();
        $pet->refresh();

        $message = $leveledUp 
            ? "¡Snoopy se divirtió mucho! ¡Subió de nivel! 🎉 (Costó {$playCost} fichitas)" 
            : "¡Snoopy se divirtió mucho jugando contigo! 🎮 (Costó {$playCost} fichitas). Tienes {$pet->coins} fichitas restantes.";

        $unlocked = $this->syncAchievements(['pet']);

        return redirect()->route('pet.index')->with('success', $message . $this->achievementMessage($unlocked));
    }

    /**
     * Care for the pet (heal/rest).
     */
    public function care()
    {
        $pet = Pet::where('user_id', Auth::id())->firstOrFail();
        
        // Cost to care: 20 coins - Check FIRST
        $careCost = 20;
        
        if ($pet->coins < $careCost) {
            return redirect()->route('pet.index')->with('error', "No tienes suficientes fichitas. Necesitas {$careCost} fichitas para cuidar a Snoopy. ¡Escribe en tu diario con estados de ánimo felices para ganar más! 💰");
        }
        
        // Check if can care (cooldown of 2 hours)
        if ($pet->last_cared_at && $pet->last_cared_at->diffInMinutes(now()) < 120) {
            return redirect()->route('pet.index')->with('error', 'Snoopy ya descansó recientemente.');
        }

        $pet->coins = max(0, $pet->coins - $careCost);
        $pet->health = min(100, $pet->health + 20);
        $pet->energy = min(100, $pet->energy + 30);
        $pet->happiness = min(100, $pet->happiness + 15);
        $pet->last_cared_at = now();
        
        $leveledUp = $pet->addExperience(15);
        $pet->save();
        $pet->refresh();

        $message = $leveledUp 
            ? "¡Snoopy se siente mucho mejor! ¡Subió de nivel! 🎉 (Costó {$careCost} fichitas)" 
            : "¡Snoopy se siente mucho mejor después de descansar! 💤 (Costó {$careCost} fichitas). Tienes {$pet->coins} fichitas restantes.";

        $unlocked = $this->syncAchievements(['pet']);

        return redirect()->route('pet.index')->with('success', $message . $this->achievementMessage($unlocked));
    }

    /**
     * Rename the pet.
     */
    public function rename(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $pet = Pet::where('user_id', Auth::id())->firstOrFail();
        $pet->name = $validated['name'];
        $pet->save();

        return back()->with('success', '¡Nombre actualizado!');
    }
}
