@extends('layouts.app')
@section('title', 'Accueil — TicketFile')

@section('page-header')
<div>
    <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mb-1">Usager</p>
    <h1 class="text-2xl font-black text-gray-900 dark:text-white">Bonjour, {{ Auth::user()->name }} 👋</h1>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
    ✅ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-5 flex items-center gap-2 rounded-xl bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
    ❌ {{ session('error') }}
</div>
@endif

@if($myTicket)
<div class="mb-6 bg-indigo-600 rounded-2xl p-6 text-white flex items-center justify-between gap-4 flex-wrap">
    <div>
        <p class="text-xs font-semibold text-indigo-300 uppercase tracking-widest mb-1">Votre ticket actif</p>
        <p class="text-5xl font-black leading-none">#{{ str_pad($myTicket->ticket_number, 3, '0', STR_PAD_LEFT) }}</p>
        <p class="text-sm text-indigo-200 mt-2">{{ $myTicket->service->name }}</p>
    </div>
    <div class="flex flex-col gap-2">
        <a href="{{ route('usager.ticket', $myTicket->id) }}"
           class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold text-sm px-4 py-2.5 rounded-xl hover:bg-indigo-50 transition">
            👁️ Suivre mon ticket
        </a>
        <form action="{{ route('usager.ticket.cancel', $myTicket->id) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-700 hover:bg-indigo-800 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition">
                ✖ Annuler
            </button>
        </form>
    </div>
</div>
@endif

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h2 class="font-bold text-gray-900 dark:text-white">Services disponibles</h2>
        <p class="text-xs text-gray-400 mt-0.5">Choisissez un service pour prendre un ticket</p>
    </div>

    @if($services->isEmpty())
    <div class="py-12 text-center">
        <p class="text-3xl mb-2">😔</p>
        <p class="text-sm text-gray-400">Aucun service disponible pour le moment.</p>
    </div>
    @else
    <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($services as $service)
        <div class="rounded-xl border border-gray-100 dark:border-gray-800 bg-slate-50 dark:bg-gray-800/50 p-5 flex flex-col gap-4">
            <div>
                <p class="font-bold text-gray-900 dark:text-white">{{ $service->name }}</p>
                @if($service->description)
                <p class="text-xs text-gray-400 mt-1">{{ $service->description }}</p>
                @endif
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    <span class="inline-flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                        {{ $service->waiting }} en attente
                    </span>
                </p>
            </div>
            @if($myTicket)
            <button disabled class="w-full bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-semibold text-sm py-2.5 rounded-xl cursor-not-allowed">
                Ticket déjà actif
            </button>
            @else
            <form action="{{ route('usager.ticket.take') }}" method="POST">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-semibold text-sm py-2.5 rounded-xl transition">
                    🎫 Prendre un ticket
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
