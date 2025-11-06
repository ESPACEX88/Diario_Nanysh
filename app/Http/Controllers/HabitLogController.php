<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HabitLogController extends Controller
{
    /**
     * Toggle habit log for a specific date.
     */
    public function toggle(Request $request, string $habitId)
    {
        $habit = Habit::where('user_id', Auth::id())
            ->findOrFail($habitId);

        $date = $request->input('date', Carbon::today()->toDateString());

        $log = HabitLog::where('habit_id', $habit->id)
            ->where('user_id', Auth::id())
            ->whereDate('completed_at', $date)
            ->first();

        if ($log) {
            // If log exists, delete it (untoggle)
            $log->delete();
            $message = 'Hábito desmarcado para esta fecha.';
        } else {
            // Create new log entry
            HabitLog::create([
                'habit_id' => $habit->id,
                'user_id' => Auth::id(),
                'completed_at' => $date,
                'notes' => $request->input('notes'),
            ]);
            $message = 'Hábito marcado como completado.';
        }

        return back()->with('success', $message);
    }
}
