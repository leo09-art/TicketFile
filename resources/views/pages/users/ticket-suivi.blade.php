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
        <div id="ticket-live-root" data-endpoint="{{ route('usager.ticket.data', $ticket) }}">
        <p id="ticket-number" class="text-8xl font-black text-indigo-600 dark:text-indigo-400 leading-none">#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
            Service : <span id="ticket-service" class="font-bold text-gray-800 dark:text-white">{{ $ticket->service->name }}</span>
        </p>
        <p id="ticket-counter" class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $ticket->counter?->name ?? '' }}</p>

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

        <div id="ticket-status" data-status="{{ $ticket->status }}" class="mt-5 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">
            @if($ticket->status === 'en_attente')
                <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
            @endif
            <span id="ticket-status-label">{{ $statusLabels[$ticket->status] ?? $ticket->status }}</span>
        </div>
        </div>
    </div>

    {{-- Position + temps --}}
    <div id="queue-live-block" class="{{ in_array($ticket->status, ['en_attente', 'appele']) ? '' : 'hidden' }}">
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 text-center">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Position</p>
            <p id="ticket-position" class="text-4xl font-black text-gray-900 dark:text-white">{{ $position }}</p>
            <p id="ticket-position-label" class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                {{ $position === 0 ? "C'est votre tour !" : 'personne(s) avant vous' }}
            </p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5 text-center">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">Temps estimé</p>
            <p id="ticket-eta" class="text-4xl font-black text-indigo-600 dark:text-indigo-400">{{ $estimatedTime }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">minutes</p>
        </div>
    </div>

    <div id="ticket-turn-alert" class="{{ $position === 0 && $ticket->status === 'appele' ? '' : 'hidden' }} bg-emerald-50 dark:bg-emerald-950 border-2 border-emerald-300 dark:border-emerald-700 rounded-2xl p-6 text-center">
        <p class="text-4xl mb-2"></p>
        <p class="text-lg font-black text-emerald-700 dark:text-emerald-400">C'est votre tour !</p>
        <p class="text-sm text-emerald-600 dark:text-emerald-500 mt-1">
            Rendez-vous au <strong id="ticket-turn-counter">{{ $ticket->counter?->name ?? 'guichet' }}</strong>
        </p>
    </div>
    </div>

    {{-- Annuler --}}
    <form id="cancel-ticket-form" class="{{ $ticket->status === 'en_attente' && $ticket->user_id === Auth::id() ? '' : 'hidden' }}" action="{{ route('usager.ticket.cancel', $ticket) }}" method="POST"
        onsubmit="return confirm('Annuler ce ticket ?')">
        @csrf @method('PATCH')
        <button type="submit" class="w-full bg-red-50 dark:bg-red-950 hover:bg-red-100 dark:hover:bg-red-900 text-red-600 dark:text-red-400 font-semibold text-sm py-3 rounded-xl transition">
            Annuler mon ticket
        </button>
    </form>

    <a href="{{ route('usager.dashboard') }}"
        class="block text-center bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold text-sm py-3 rounded-xl transition">
        ← Retour à l'accueil
    </a>

    <p class="text-center text-xs text-gray-400 dark:text-gray-500">Mise à jour en temps réel (toutes les 5 secondes)</p>
</div>

<script>
(() => {
    const root = document.getElementById('ticket-live-root');
    if (!root) return;

    const endpoint = root.dataset.endpoint;
    const statusClass = {
        en_attente: 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
        appele: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400',
        traite: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400',
        absent: 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
        annule: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'
    };

    async function refreshTicket() {
        const response = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) return;

        const data = await response.json();

        document.getElementById('ticket-number').textContent = `#${data.ticket_number}`;
        document.getElementById('ticket-service').textContent = data.service_name || '—';
        document.getElementById('ticket-counter').textContent = data.counter_name || '';

        const statusEl = document.getElementById('ticket-status');
        statusEl.className = `mt-5 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ${statusClass[data.status] || 'bg-gray-100 text-gray-600'}`;
        document.getElementById('ticket-status-label').textContent = data.status_label;

        const queueVisible = data.status === 'en_attente' || data.status === 'appele';
        const queueBlock = document.getElementById('queue-live-block');
        queueBlock.classList.toggle('hidden', !queueVisible);

        document.getElementById('ticket-position').textContent = data.position;
        document.getElementById('ticket-position-label').textContent = data.position === 0 ? "C'est votre tour !" : 'personne(s) avant vous';
        document.getElementById('ticket-eta').textContent = data.estimated_time;

        const alert = document.getElementById('ticket-turn-alert');
        const showAlert = data.position === 0 && data.status === 'appele';
        alert.classList.toggle('hidden', !showAlert);
        document.getElementById('ticket-turn-counter').textContent = data.counter_name || 'guichet';

        document.getElementById('cancel-ticket-form').classList.toggle('hidden', !data.can_cancel);
    }

    refreshTicket();
    setInterval(refreshTicket, 5000);
})();
</script>
@endsection
