<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function index()
    {
        $counters = Counter::with(['service', 'agent'])->get();
        return view('counters.index', compact('counters'));
    }

    public function create()
    {
        $services = Service::active()->get();
        $agents = User::where('role', 'agent')->get();
        return view('counters.create', compact('services', 'agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        Counter::create([
            'name' => $request->name,
            'service_id' => $request->service_id,
            'agent_user_id' => $request->agent_user_id,
        ]);

        return redirect()->route('counters.index')->with('success', 'Guichet créé');
    }

    public function show(Counter $counter)
    {
        $tickets = $counter->tickets()
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at')
            ->get();
        return view('counters.show', compact('counter', 'tickets'));
    }

    public function edit(Counter $counter)
    {
        $services = Service::active()->get();
        $agents = User::where('role', 'agent')->get();
        return view('counters.edit', compact('counter', 'services', 'agents'));
    }

    public function update(Request $request, Counter $counter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        $counter->update([
            'name' => $request->name,
            'service_id' => $request->service_id,
            'agent_user_id' => $request->agent_user_id,
        ]);

        return redirect()->route('counters.index')->with('success', 'Guichet mis à jour');
    }

    public function destroy(Counter $counter)
    {
        $counter->delete();
        return redirect()->route('counters.index')->with('success', 'Guichet supprimé');
    }
}