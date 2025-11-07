<?php

namespace App\Http\Controllers;

use App\Models\CycleTracking;
use App\Services\CycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class CycleTrackingController extends Controller
{
    public function index()
    {
        $trackings = CycleTracking::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        
        $cycleService = new CycleService();
        $stats = $cycleService->getCycleStats(Auth::user());

        return Inertia::render('Cycle/Index', [
            'trackings' => $trackings,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $cycleService = new CycleService();
        $today = now();
        $cycleInfo = $cycleService->calculateCycleInfo(Auth::user(), $today);
        $stats = $cycleService->getCycleStats(Auth::user());
        
        return Inertia::render('Cycle/Create', [
            'suggestedPhase' => $cycleInfo['phase'],
            'suggestedCycleDay' => $cycleInfo['cycle_day'],
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'phase' => 'nullable|in:menstrual,follicular,ovulation,luteal',
            'cycle_day' => 'nullable|integer|min:1|max:35',
            'symptoms' => 'nullable|array',
            'mood' => 'nullable|string',
            'flow_level' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        $date = Carbon::parse($validated['date']);
        $cycleService = new CycleService();
        
        // Si no se proporcionó fase o día del ciclo, calcularlos automáticamente
        if (empty($validated['phase']) || empty($validated['cycle_day'])) {
            $cycleInfo = $cycleService->calculateCycleInfo(Auth::user(), $date);
            $validated['phase'] = $validated['phase'] ?? $cycleInfo['phase'];
            $validated['cycle_day'] = $validated['cycle_day'] ?? $cycleInfo['cycle_day'];
        }

        $validated['user_id'] = Auth::id();
        CycleTracking::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => $validated['date'],
            ],
            $validated
        );

        return redirect()->route('cycle.index')
            ->with('success', 'Registro guardado exitosamente');
    }

    public function show($id)
    {
        $tracking = CycleTracking::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Cycle/Show', ['tracking' => $tracking]);
    }

    public function edit($id)
    {
        $tracking = CycleTracking::where('user_id', Auth::id())->findOrFail($id);
        return Inertia::render('Cycle/Edit', ['tracking' => $tracking]);
    }

    public function update(Request $request, $id)
    {
        $tracking = CycleTracking::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'phase' => 'nullable|in:menstrual,follicular,ovulation,luteal',
            'cycle_day' => 'nullable|integer|min:1|max:35',
            'symptoms' => 'nullable|array',
            'mood' => 'nullable|string',
            'flow_level' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        $tracking->update($validated);

        return redirect()->route('cycle.index')
            ->with('success', 'Registro actualizado');
    }

    public function destroy($id)
    {
        $tracking = CycleTracking::where('user_id', Auth::id())->findOrFail($id);
        $tracking->delete();

        return redirect()->route('cycle.index')
            ->with('success', 'Registro eliminado');
    }
}
