@extends('layouts.app')
@section('title', 'Mon Guichet — TicketFile')

@section('page-header')
<div class="flex items-center justify-between">
    <div>
        <p class="text-xs font-semibold text-amber-500 uppercase tracking-widest mb-1">Agent</p>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ $counter?->name ?? 'Mon guichet' }}</h1>
    </div>
    @if($counter)
    <div class="flex items-center gap-3">
        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $counter->service?->name }}</span>
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Actif
        </span>
    </div>
    @endif
</div>
@endsection

@section('content')

@if(!$counter)
<div class="bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-2xl p-8 text-center">
    <p class="font-bold text-amber-800 dark:text-amber-400">Aucun guichet assigné</p>
    <p class="text-sm text-amber-600 dark:text-amber-500 mt-1">Contactez l'administrateur pour qu'il vous assigne un guichet.</p>
</div>
@else

@if(session('success'))
<div class="mb-5 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-5 rounded-xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
    {{ session('error') }}
</div>
@endif

<div class="grid gap-5 lg:grid-cols-[1fr_260px]">
    <div class="space-y-5">

        {{-- Ticket en cours --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-6">
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-5">Ticket en cours</p>

            <div class="flex items-start justify-between gap-6 flex-wrap">
                <div>
                    @if($currentTicket)
                    <p class="text-7xl font-black text-indigo-600 dark:text-indigo-400 leading-none">
                        #{{ str_pad($currentTicket->ticket_number, 3, '0', STR_PAD_LEFT) }}
                    </p>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-3">{{ $currentTicket->service->name }}</p>
                    @if($currentTicket->user)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $currentTicket->user->name }}</p>
                    @endif
                    @if($currentTicket->called_at)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Appelé à {{ $currentTicket->called_at->format('H:i') }}
                        —
                        <span id="elapsed-time"
                              data-called="{{ $currentTicket->called_at->timestamp }}"
                              data-absent-url="{{ route('agent.ticket.absent', $currentTicket) }}"
                              data-csrf="{{ csrf_token() }}"
                              class="font-semibold text-amber-500">
                            0s
                        </span>
                    </p>
                    {{-- Barre de progression 60s --}}
                    <div class="mt-2 w-48 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div id="timer-bar" class="h-full bg-amber-400 rounded-full transition-all duration-1000" style="width:0%"></div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Absent automatique après <span id="remaining">60</span>s</p>
                    @endif
                    @else
                    <p class="text-5xl font-black text-gray-200 dark:text-gray-700 leading-none">—</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-3">Aucun ticket en cours</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Appuyez sur "Appeler le suivant" pour commencer</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2.5 min-w-[180px]">
                    <form action="{{ route('agent.call-next', $counter) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-semibold text-sm py-3 px-4 rounded-xl transition shadow-sm">
                            Appeler le suivant
                        </button>
                    </form>

                    @if($currentTicket)
                    <form action="{{ route('agent.ticket.treated', $currentTicket) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="w-full flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 active:scale-[0.98] text-white font-semibold text-sm py-3 px-4 rounded-xl transition">
                            Marquer traité
                        </button>
                    </form>

                    <form id="absent-form" action="{{ route('agent.ticket.absent', $currentTicket) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="w-full flex items-center justify-center bg-red-50 dark:bg-red-950 hover:bg-red-100 dark:hover:bg-red-900 active:scale-[0.98] text-red-600 dark:text-red-400 font-semibold text-sm py-3 px-4 rounded-xl transition">
                            Absent
                        </button>
                    </form>
                    @else
                    <button disabled class="w-full bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 font-semibold text-sm py-3 px-4 rounded-xl cursor-not-allowed">Marquer traité</button>
                    <button disabled class="w-full bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 font-semibold text-sm py-3 px-4 rounded-xl cursor-not-allowed">Absent</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tickets absents rappelables --}}
        @if($absentTickets->isNotEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-red-200 dark:ring-red-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-red-100 dark:border-red-800 flex items-center justify-between">
                <h2 class="font-bold text-red-700 dark:text-red-400">Absents — à rappeler</h2>
                <span class="bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $absentTickets->count() }}
                </span>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($absentTickets as $ticket)
                    <tr class="hover:bg-red-50/50 dark:hover:bg-red-900/10 transition">
                        <td class="px-6 py-3.5 font-black text-red-500">#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-3.5 text-gray-700 dark:text-gray-300">{{ $ticket->user?->name ?? 'Invité' }}</td>
                        <td class="px-6 py-3.5 text-gray-400 text-xs">{{ $ticket->updated_at->format('H:i') }}</td>
                        <td class="px-6 py-3.5 text-right">
                            <form action="{{ route('agent.ticket.recall', $ticket) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="bg-amber-100 dark:bg-amber-900/40 hover:bg-amber-200 dark:hover:bg-amber-900 text-amber-700 dark:text-amber-400 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                    Rappeler
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- File d'attente --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h2 class="font-bold text-gray-900 dark:text-white">File d'attente</h2>
                <span class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400 text-xs font-bold px-3 py-1 rounded-full">
                    {{ $waitingTickets->count() }} en attente
                </span>
            </div>

            @if($waitingTickets->isEmpty())
            <div class="py-10 text-center">
                <p class="text-sm text-gray-400">File vide — aucun ticket en attente.</p>
            </div>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">N°</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Usager</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Attente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Position</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($waitingTickets as $i => $ticket)
                    <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition {{ $i === 0 ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
                        <td class="px-6 py-3.5 font-black text-indigo-600 dark:text-indigo-400">
                            #{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}
                            @if($i === 0)<span class="ml-1 text-xs text-indigo-400">suivant</span>@endif
                        </td>
                        <td class="px-6 py-3.5 font-medium text-gray-800 dark:text-gray-200">{{ $ticket->user?->name ?? 'Invité' }}</td>
                        <td class="px-6 py-3.5 text-gray-400 dark:text-gray-500 text-xs">{{ $ticket->created_at->format('H:i') }}</td>
                        <td class="px-6 py-3.5 text-xs {{ $ticket->created_at->diffInMinutes(now()) > 15 ? 'text-red-500 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $ticket->created_at->diffInMinutes(now()) }} min
                        </td>
                        <td class="px-6 py-3.5 text-xs text-gray-400 dark:text-gray-500">{{ $i + 1 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-5">
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Aujourd'hui</p>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span> En attente
                    </div>
                    <span class="font-black text-indigo-600 dark:text-indigo-400 text-xl">{{ $todayStats['en_attente'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Traités
                    </div>
                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-xl">{{ $todayStats['traite'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span> Absents
                    </div>
                    <span class="font-black text-red-500 dark:text-red-400 text-xl">{{ $todayStats['absent'] }}</span>
                </div>
                @if($todayStats['traite'] > 0 && $avgTreatmentTime > 0)
                <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-400 mb-1">Temps moyen de traitement</p>
                    <p class="font-black text-gray-700 dark:text-gray-300">{{ $avgTreatmentTime }} min</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-indigo-950 dark:bg-gray-800 rounded-2xl p-5 text-white">
            <p class="text-xs font-semibold text-indigo-400 dark:text-gray-400 uppercase tracking-widest mb-2">Mon guichet</p>
            <p class="text-lg font-black">{{ $counter->name }}</p>
            <p class="text-sm text-indigo-300 dark:text-gray-300 mt-1">{{ $counter->service?->name ?? '—' }}</p>
        </div>

        <p class="text-center text-xs text-gray-400 dark:text-gray-500">Actualisation auto toutes les 20s</p>
    </div>
</div>

<meta http-equiv="refresh" content="20">

<script>
const el = document.getElementById('elapsed-time');
const bar = document.getElementById('timer-bar');
const remaining = document.getElementById('remaining');
const LIMIT = 60; // 1 minute

if (el) {
    const calledAt = parseInt(el.dataset.called) * 1000;
    const absentUrl = el.dataset.absentUrl;
    const csrf = el.dataset.csrf;

    const tick = () => {
        const diff = Math.floor((Date.now() - calledAt) / 1000);
        const s = diff % 60;
        const m = Math.floor(diff / 60);
        el.textContent = m > 0 ? `${m}min ${s}s` : `${s}s`;

        const pct = Math.min((diff / LIMIT) * 100, 100);
        if (bar) {
            bar.style.width = pct + '%';
            if (pct >= 80) bar.classList.replace('bg-amber-400', 'bg-red-500');
        }
        if (remaining) remaining.textContent = Math.max(LIMIT - diff, 0);

        // Auto absent après 60s
        if (diff >= LIMIT) {
            const form = document.getElementById('absent-form');
            if (form) {
                form.submit();
            } else {
                // fallback fetch
                fetch(absentUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '_method=PATCH'
                }).then(() => location.reload());
            }
        }
    };

    tick();
    setInterval(tick, 1000);
}
</script>

@endif
@endsection