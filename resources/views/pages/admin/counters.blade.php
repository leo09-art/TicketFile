@extends('layouts.app')
@section('title', 'Guichets — TicketFile')

@section('page-header')
<div class="flex items-center justify-between">
    <div>
        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mb-1">Admin</p>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">Guichets</h1>
    </div>
    <button onclick="document.getElementById('modal-counter').classList.remove('hidden')"
        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
        + Nouveau guichet
    </button>
</div>
@endsection

@section('content')
@if(session('success'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
     {{ session('success') }}
</div>
@endif

@if($counters->isEmpty())
<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 py-16 text-center">
    <p class="text-4xl mb-3">🖥️</p>
    <p class="font-semibold text-gray-700 dark:text-gray-300">Aucun guichet configuré</p>
    <p class="text-sm text-gray-400 mt-1">Créez votre premier guichet.</p>
</div>
@else
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach($counters as $counter)
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 flex flex-col gap-4">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/40 text-xl">🖥️</div>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white">{{ $counter->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $counter->service?->name ?? 'Aucun service' }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5">
            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 font-black text-xs">
                {{ strtoupper(substr($counter->agent?->name ?? 'N', 0, 1)) }}
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $counter->agent?->name ?? 'Non assigné' }}</p>
                <p class="text-xs text-gray-400">Agent</p>
            </div>
        </div>

        <form action="{{ route('admin.counters.destroy', $counter) }}" method="POST"
            onsubmit="return confirm('Supprimer ce guichet ?')">
            @csrf @method('DELETE')
            <button type="submit"
                class="w-full py-2 rounded-xl bg-red-50 dark:bg-red-950 hover:bg-red-100 dark:hover:bg-red-900 text-red-600 dark:text-red-400 text-xs font-semibold transition">
                🗑 Supprimer
            </button>
        </form>
    </div>
    @endforeach
</div>
@endif

{{-- Modal création --}}
<div id="modal-counter" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-2xl p-7 ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-black text-gray-900 dark:text-white">Nouveau guichet</h2>
            <button onclick="document.getElementById('modal-counter').classList.add('hidden')"
                class="h-8 w-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 transition text-lg">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.counters.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" placeholder="Ex : Guichet 1" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Service <span class="text-red-500">*</span></label>
                <select name="service_id" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                    <option value="">-- Sélectionner --</option>
                    @foreach($services as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Agent <span class="text-gray-400 font-normal">(optionnel)</span></label>
                <select name="agent_user_id"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                    <option value="">-- Aucun agent --</option>
                    @foreach($agents as $a)
                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-counter').classList.add('hidden')"
                    class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-300 transition">Annuler</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">Créer</button>
            </div>
        </form>
    </div>
</div>
@endsection
