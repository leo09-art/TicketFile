<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // Dashboard admin
    public function adminDashboard()
    {
        $stats = [
            'users'    => User::count(),
            'tickets'  => Ticket::today()->count(),
            'services' => Service::count(),
            'counters' => Counter::count(),
        ];
        $recentTickets = Ticket::with(['service', 'user', 'counter'])
            ->today()
            ->latest()
            ->take(10)
            ->get();
        return view('pages.admin.dashboard-admin', compact('stats', 'recentTickets'));
    }

    // Dashboard agent
    public function agentDashboard()
    {
        $counter = Counter::where('agent_user_id', Auth::id())->with('service')->first();

        if (!$counter) {
            return view('pages.agent.dashboard-agent', [
                'counter'          => null,
                'currentTicket'    => null,
                'waitingTickets'   => collect(),
                'absentTickets'    => collect(),
                'todayStats'       => ['traite' => 0, 'absent' => 0, 'en_attente' => 0],
                'avgTreatmentTime' => 0,
            ]);
        }

        $currentTicket = Ticket::where('counter_id', $counter->id)
            ->where('status', Ticket::STATUS_APPELLE)
            ->today()
            ->latest('called_at')
            ->first();

        $waitingTickets = Ticket::where('service_id', $counter->service_id)
            ->where('status', Ticket::STATUS_EN_ATTENTE)
            ->today()
            ->orderBy('ticket_number')
            ->with(['service', 'user'])
            ->get();

        // Tickets absents du jour pour ce guichet (rappelables)
        $absentTickets = Ticket::where('counter_id', $counter->id)
            ->where('status', Ticket::STATUS_ABSENT)
            ->today()
            ->with('user')
            ->get();

        $treatedToday = Ticket::where('counter_id', $counter->id)
            ->where('status', Ticket::STATUS_TRAITE)
            ->today()
            ->whereNotNull('called_at')
            ->whereNotNull('treated_at')
            ->get();

        $avgTreatmentTime = $treatedToday->isNotEmpty()
            ? round($treatedToday->avg(fn($t) => $t->called_at->diffInMinutes($t->treated_at)))
            : 0;

        $todayStats = [
            'traite'     => Ticket::where('counter_id', $counter->id)->where('status', Ticket::STATUS_TRAITE)->today()->count(),
            'absent'     => Ticket::where('counter_id', $counter->id)->where('status', Ticket::STATUS_ABSENT)->today()->count(),
            'en_attente' => Ticket::where('service_id', $counter->service_id)->where('status', Ticket::STATUS_EN_ATTENTE)->today()->count(),
        ];

        return view('pages.agent.dashboard-agent', compact(
            'counter', 'currentTicket', 'waitingTickets', 'absentTickets', 'todayStats', 'avgTreatmentTime'
        ));
    }

    // Appeler le ticket suivant
    public function callNext(Counter $counter)
    {
        // Marquer le ticket actuellement appelé comme traité automatiquement
        $current = Ticket::where('counter_id', $counter->id)
            ->where('status', Ticket::STATUS_APPELLE)
            ->today()
            ->latest('called_at')
            ->first();

        if ($current) {
            $current->update(['status' => Ticket::STATUS_TRAITE, 'treated_at' => now()]);
        }

        // Prendre le prochain ticket en attente pour le service de ce guichet (FIFO)
        $next = Ticket::where('service_id', $counter->service_id)
            ->where('status', Ticket::STATUS_EN_ATTENTE)
            ->today()
            ->orderBy('ticket_number')
            ->first();

        if ($next) {
            $next->update([
                'status'     => Ticket::STATUS_APPELLE,
                'counter_id' => $counter->id,
                'called_at'  => now(),
            ]);
        }

        return back()->with('success', $next ? "Ticket #{$next->ticket_number} appelé." : 'Aucun ticket en attente.');
    }

    // Marquer traité
    public function markTreated(Ticket $ticket)
    {
        $ticket->update(['status' => Ticket::STATUS_TRAITE, 'treated_at' => now()]);
        return back()->with('success', "Ticket #{$ticket->ticket_number} marqué comme traité.");
    }

    // Marquer absent
    public function markAbsent(Ticket $ticket)
    {
        $ticket->update(['status' => Ticket::STATUS_ABSENT]);
        return back()->with('success', "Ticket #{$ticket->ticket_number} marqué absent.");
    }

    // Rappeler un ticket absent (le remettre en appelé)
    public function recallTicket(Ticket $ticket)
    {
        if ($ticket->status !== Ticket::STATUS_ABSENT) {
            return back()->with('error', 'Ce ticket ne peut pas être rappelé.');
        }
        $ticket->update(['status' => Ticket::STATUS_APPELLE, 'called_at' => now()]);
        return back()->with('success', "Ticket #{$ticket->ticket_number} rappelé.");
    }

    // Dashboard usager
    public function usagerDashboard()
    {
        // Seulement les services qui ont au moins un guichet avec un agent assigné
        $serviceIdsWithCounter = Counter::whereNotNull('agent_user_id')->pluck('service_id')->unique();

        $services = Service::active()
            ->whereIn('id', $serviceIdsWithCounter)
            ->get()
            ->map(function ($s) {
                $s->waiting = $s->tickets()->where('status', Ticket::STATUS_EN_ATTENTE)->today()->count();
                return $s;
            });

        $myTicket = Ticket::where('user_id', Auth::id())
            ->today()
            ->whereIn('status', [Ticket::STATUS_EN_ATTENTE, Ticket::STATUS_APPELLE])
            ->with('service')
            ->latest()
            ->first();

        return view('pages.users.dashboard-user', compact('services', 'myTicket'));
    }

    // Prendre un ticket
    public function take(Request $request)
    {
        $request->validate(['service_id' => 'required|exists:services,id']);

        // Vérifier qu'un guichet avec agent existe pour ce service
        $hasCounter = Counter::where('service_id', $request->service_id)
            ->whereNotNull('agent_user_id')
            ->exists();

        if (!$hasCounter) {
            return back()->with('error', 'Ce service n\'est pas disponible pour le moment.');
        }

        $existing = Ticket::where('user_id', Auth::id())
            ->today()
            ->whereIn('status', [Ticket::STATUS_EN_ATTENTE, Ticket::STATUS_APPELLE])
            ->first();

        if ($existing) {
            return back()->with('error', 'Vous avez déjà un ticket actif aujourd\'hui.');
        }

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'service_id' => $request->service_id,
            'user_id' => Auth::id(),
            'status' => Ticket::STATUS_EN_ATTENTE,
        ]);

        return redirect()->route('usager.ticket', $ticket->id)->with('success', "Ticket #{$ticket->ticket_number} créé !");
    }

    // Suivi ticket usager
    public function ticketSuivi(Ticket $ticket)
    {
        $ticket->load('service', 'counter');
        $position = $ticket->getPositionInQueue();
        $estimatedTime = $position * 5;
        return view('pages.users.ticket-suivi', compact('ticket', 'position', 'estimatedTime'));
    }

    // Annuler ticket
    public function cancel(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            return back()->with('error', 'Action non autorisée.');
        }
        $ticket->update(['status' => Ticket::STATUS_ANNULE]);
        return redirect()->route('usager.dashboard')->with('success', 'Ticket annulé.');
    }

    // Méthodes CRUD standards
    public function create()
    {
        $services = Service::active()->get();
        return view('tickets.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate(['service_id' => 'required|exists:services,id']);

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'service_id' => $request->service_id,
            'user_id' => Auth::check() ? Auth::id() : null,
            'status' => Ticket::STATUS_EN_ATTENTE,
        ]);

        return redirect()->route('tickets.show', $ticket);
    }

    public function show(Ticket $ticket)
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $position = $ticket->getPositionInQueue();
        $service = $ticket->service;

        return view('tickets.show', compact('ticket', 'position', 'service'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:en_attente,appele,traite,absent,annule',
        ]);

        $ticket->update([
            'status' => $request->status,
            'called_at' => $request->status === Ticket::STATUS_APPELLE ? now() : $ticket->called_at,
            'treated_at' => $request->status === Ticket::STATUS_TRAITE ? now() : $ticket->treated_at,
        ]);

        return redirect()->back()->with('success', 'Statut mis à jour');
    }
}
