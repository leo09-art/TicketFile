@extends('layouts.app')
@section('title', 'Mon Ticket — TicketFile')

@section('page-header')
<div>
    <p class="text-xs font-semibold text-blue-500 uppercase tracking-widest mb-1">Usager</p>
    <h1 class="text-2xl font-black text-gray-900 dark:text-white">Suivi de mon ticket</h1>
</div>
@endsection

@section('content')
<div class="max-w-lg mx-auto space-y-4">

    {{-- Numéro --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-8 text-center">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3">Votre numéro</p>
        <p class="text-8xl font-black text-indigo-600 dark:text-indigo-400 leading-none">#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
            Service : <span class="font-bold text-gray-800 dark:text-white">{{ $ticket->service->name }}</span>
        </p>
        @if($ticket->counter)
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $ticket->counter->name }}</p>
        @endif

        @php
            $statusColors = [
                'en_attente' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                'appele'     => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
                'traite'     => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
                'absent'     => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
                'annule'     => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
            ];
            $statusLabels = [
                'en_attente' => 'En attente',
                'appele'     => 'Appelé',
                'traite'     => 'Traité',
                'absent'     => 'Absent',
                'annule'     => 'Annulé',
            ];
        @endphp

        <div class="mt-5 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">
            @if($ticket->status === 'en_attente')
                <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
            @endif
            {{ $statusLabels[$ticket->status] ?? $ticket->status }}
        </div>
    </div>

    {{-- Position + temps --}}
    @if(in_array($ticket->status, ['en_attente', 'appele']))
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 text-center">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Position</p>
            <p class="text-4xl font-black text-gray-900 dark:text-white">{{ $position }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                {{ $position === 0 ? "C'est votre tour !" : 'personne(s) avant vous' }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 text-center">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Temps estimé</p>
            <p class="text-4xl font-black text-indigo-600 dark:text-indigo-400">{{ $estimatedTime }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">minutes</p>
        </div>
    </div>

    @if($position === 0 && $ticket->status === 'appele')
    <div class="bg-emerald-50 dark:bg-emerald-950 border-2 border-emerald-300 dark:border-emerald-700 rounded-2xl p-6 text-center">
        <p class="text-4xl mb-2"></p>
        <p class="text-lg font-black text-emerald-700 dark:text-emerald-400">C'est votre tour !</p>
        <p class="text-sm text-emerald-600 dark:text-emerald-500 mt-1">
            Rendez-vous au <strong>{{ $ticket->counter?->name ?? 'guichet' }}</strong>
        </p>
    </div>
    @endif
    @endif

    {{-- Annuler --}}
    @if($ticket->status === 'en_attente' && $ticket->user_id === Auth::id())
    <form action="{{ route('usager.ticket.cancel', $ticket) }}" method="POST"
        onsubmit="return confirm('Annuler ce ticket ?')">
        @csrf @method('PATCH')
        <button type="submit" class="w-full bg-red-50 dark:bg-red-950 hover:bg-red-100 dark:hover:bg-red-900 text-red-600 dark:text-red-400 font-semibold text-sm py-3 rounded-xl transition">
            Annuler mon ticket
        </button>
    </form>
    @endif

    <a href="{{ route('usager.dashboard') }}"
        class="block text-center bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold text-sm py-3 rounded-xl transition">
        ← Retour à l'accueil
    </a>

    @if(in_array($ticket->status, ['en_attente', 'appele']))
    <p class="text-center text-xs text-gray-400 dark:text-gray-500">Actualisation automatique toutes les 30 secondes</p>
    <meta http-equiv="refresh" content="30">
    @endif
</div>
@endsection
