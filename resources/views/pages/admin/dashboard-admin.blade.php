@extends('layouts.app')
@section('title', 'Dashboard Admin — TicketFile')

@section('page-header')
<div class="flex items-center justify-between">
    <div>
        <p class="text-xs font-semibold text-purple-500 uppercase tracking-widest mb-1">Admin</p>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">Dashboard</h1>
    </div>
    <span class="text-xs text-gray-400">{{ now()->format('d/m/Y') }}</span>
</div>
@endsection

@section('content')

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Utilisateurs</p>
        <p class="text-4xl font-black text-gray-900 dark:text-white">{{ $stats['users'] }}</p>
        <p class="text-xs text-gray-400 mt-1">comptes enregistrés</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Tickets aujourd'hui</p>
        <p class="text-4xl font-black text-indigo-600 dark:text-indigo-400">{{ $stats['tickets'] }}</p>
        <p class="text-xs text-gray-400 mt-1">tickets créés</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Services</p>
        <p class="text-4xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['services'] }}</p>
        <p class="text-xs text-gray-400 mt-1">services configurés</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Guichets</p>
        <p class="text-4xl font-black text-amber-500 dark:text-amber-400">{{ $stats['counters'] }}</p>
        <p class="text-xs text-gray-400 mt-1">guichets configurés</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl shadow-sm ring-1 p-5 bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 ring-amber-200 dark:ring-amber-800">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3 opacity-70">En attente</p>
        <p class="text-4xl font-black">{{ $recentTickets->where('status','en_attente')->count() }}</p>
    </div>
    <div class="rounded-2xl shadow-sm ring-1 p-5 bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 ring-blue-200 dark:ring-blue-800">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3 opacity-70">Appelés</p>
        <p class="text-4xl font-black">{{ $recentTickets->where('status','appele')->count() }}</p>
    </div>
    <div class="rounded-2xl shadow-sm ring-1 p-5 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 ring-emerald-200 dark:ring-emerald-800">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3 opacity-70">Traités</p>
        <p class="text-4xl font-black">{{ $recentTickets->where('status','traite')->count() }}</p>
    </div>
    <div class="rounded-2xl shadow-sm ring-1 p-5 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 ring-red-200 dark:ring-red-800">
        <p class="text-xs font-semibold uppercase tracking-widest mb-3 opacity-70">Absents</p>
        <p class="text-4xl font-black">{{ $recentTickets->where('status','absent')->count() }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 dark:text-white">Tickets du jour</h2>
        <span class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-xs font-bold px-3 py-1 rounded-full">
            {{ $recentTickets->count() }} tickets
        </span>
    </div>
    @if($recentTickets->isEmpty())
    <div class="py-12 text-center">
        <p class="text-3xl mb-2">📋</p>
        <p class="text-sm text-gray-400">Aucun ticket aujourd'hui.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">N°</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Usager</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Guichet</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Créé à</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Durée traitement</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($recentTickets as $ticket)
                @php
                    $sc = ['en_attente'=>'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400','appele'=>'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400','traite'=>'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400','absent'=>'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400','annule'=>'bg-gray-100 dark:bg-gray-800 text-gray-500'];
                    $sl = ['en_attente'=>'En attente','appele'=>'Appelé','traite'=>'Traité','absent'=>'Absent','annule'=>'Annulé'];
                    $dur = ($ticket->called_at && $ticket->treated_at) ? $ticket->called_at->diffInMinutes($ticket->treated_at).' min' : null;
                @endphp
                <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition">
                    <td class="px-6 py-3.5 font-black text-indigo-600 dark:text-indigo-400">#{{ str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $ticket->user?->name ?? 'Invité' }}</td>
                    <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400 text-xs">{{ $ticket->service?->name ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400 text-xs">{{ $ticket->counter?->name ?? '—' }}</td>
                    <td class="px-6 py-3.5"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $sc[$ticket->status] ?? '' }}">{{ $sl[$ticket->status] ?? $ticket->status }}</span></td>
                    <td class="px-6 py-3.5 text-gray-400 dark:text-gray-500 text-xs">{{ $ticket->created_at->format('H:i') }}</td>
                    <td class="px-6 py-3.5 text-xs {{ $dur ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-gray-300 dark:text-gray-600' }}">{{ $dur ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <a href="{{ route('admin.services') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 flex items-center gap-4 hover:ring-indigo-300 dark:hover:ring-indigo-700 transition">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-2xl">🏢</div>
        <div><p class="font-bold text-gray-900 dark:text-white">Services</p><p class="text-xs text-gray-400">Gérer les services</p></div>
    </a>
    <a href="{{ route('admin.counters') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 flex items-center gap-4 hover:ring-indigo-300 dark:hover:ring-indigo-700 transition">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-2xl">🖥️</div>
        <div><p class="font-bold text-gray-900 dark:text-white">Guichets</p><p class="text-xs text-gray-400">Gérer les guichets</p></div>
    </a>
    <a href="{{ route('admin.users') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 flex items-center gap-4 hover:ring-indigo-300 dark:hover:ring-indigo-700 transition">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-2xl">👥</div>
        <div><p class="font-bold text-gray-900 dark:text-white">Utilisateurs</p><p class="text-xs text-gray-400">Gérer les comptes</p></div>
    </a>
</div>

@endsection