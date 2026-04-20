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
                <td class="px-6 py-4">
                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                        onsubmit="return confirm('Supprimer « {{ $service->name }} » ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold transition">🗑 Supprimer</button>
                    </form>
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
@endsection
