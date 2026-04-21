<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'created_desc');

        $services = Service::query()
            ->withCount(['tickets' => fn($q) => $q->whereDate('created_at', today())])
            ->when($sort === 'created_asc', fn($q) => $q->orderBy('created_at'))
            ->when($sort !== 'created_asc', fn($q) => $q->orderByDesc('created_at'))
            ->get();

        return view('pages.admin.services', [
            'services' => $services,
            'sort' => $sort,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        Service::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Service créé avec succès.');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return back()->with('success', 'Service modifié avec succès.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service supprimé.');
    }
}
