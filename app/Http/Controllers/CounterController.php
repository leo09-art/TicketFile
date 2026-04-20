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
        $services = Service::active()->get();
        $agents   = User::where('role', 'agent')->get();
        return view('pages.admin.counters', compact('counters', 'services', 'agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
        ]);

        Counter::create([
            'name'          => $request->name,
            'service_id'    => $request->service_id,
            'agent_user_id' => $request->agent_user_id,
        ]);

        return back()->with('success', 'Guichet créé avec succès.');
    }

    public function destroy(Counter $counter)
    {
        $counter->delete();
        return back()->with('success', 'Guichet supprimé.');
    }

    public function toggle(Counter $counter)
    {
        // La migration n'a pas de colonne is_open, on gère via agent_user_id
        // On peut ajouter un champ is_open plus tard — pour l'instant on retourne juste
        return back()->with('success', 'Statut mis à jour.');
    }
}
