@extends('layouts.app')
@section('title', 'Services — TicketFile')

@section('page-header')
<div class="flex items-center justify-between">
    <div>
        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mb-1">Admin</p>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">Services</h1>
    </div>
    <button onclick="document.getElementById('modal-service').classList.remove('hidden')"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
        + Nouveau service
    </button>
</div>
@endsection

@section('content')
@if(session('success'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
     {{ session('success') }}
</div>
@endif

<form method="GET" action="{{ route('admin.services') }}" class="mb-5 bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-4 flex flex-col sm:flex-row sm:items-end gap-3">
    <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Trier par date de création</label>
        <select name="sort"
            class="min-w-[230px] px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="created_desc" {{ $sort === 'created_desc' ? 'selected' : '' }}>Plus récents d'abord</option>
            <option value="created_asc" {{ $sort === 'created_asc' ? 'selected' : '' }}>Plus anciens d'abord</option>
        </select>
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">Appliquer</button>
        <a href="{{ route('admin.services') }}" class="px-4 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 transition">Réinitialiser</a>
    </div>
</form>

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
    @if($services->isEmpty())
    <div class="py-16 text-center">
        <p class="text-4xl mb-3">🏢</p>
        <p class="font-semibold text-gray-700 dark:text-gray-300">Aucun service configuré</p>
        <p class="text-sm text-gray-400 mt-1">Créez votre premier service.</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Service</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Description</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Tickets aujourd'hui</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Créé le</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($services as $service)
            <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-base">🏢</div>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $service->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-400 text-xs max-w-xs truncate">{{ $service->description ?? '—' }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                        {{ $service->is_active ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500' }}">
                        {{ $service->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 text-xs font-semibold px-2.5 py-1">
                        {{ $service->tickets_count }} ticket(s)
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-400 dark:text-gray-500 text-xs">{{ $service->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <button type="button"
                            onclick="document.getElementById('modal-edit-service-{{ $service->id }}').classList.remove('hidden')"
                            class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold transition">Modifier</button>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                            onsubmit="return confirm('Supprimer « {{ $service->name }} » ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold transition">🗑 Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Modal création --}}
<div id="modal-service" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl p-7 ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-black text-gray-900 dark:text-white">Nouveau service</h2>
            <button onclick="document.getElementById('modal-service').classList.add('hidden')"
                class="h-8 w-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 transition text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" placeholder="Ex : Carte d'identité" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="text-gray-400 font-normal">(optionnel)</span></label>
                <textarea name="description" rows="2" placeholder="Courte description..."
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-service').classList.add('hidden')"
                    class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 transition">Annuler</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">Créer</button>
            </div>
        </form>
    </div>
</div>

@foreach($services as $service)
<div id="modal-edit-service-{{ $service->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl p-7 ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-black text-gray-900 dark:text-white">Modifier le service</h2>
            <button type="button" onclick="document.getElementById('modal-edit-service-{{ $service->id }}').classList.add('hidden')"
                class="h-8 w-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 transition text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $service->name }}" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                <textarea name="description" rows="2"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none">{{ $service->description }}</textarea>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                Service actif
            </label>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-service-{{ $service->id }}').classList.add('hidden')"
                    class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 transition">Annuler</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
