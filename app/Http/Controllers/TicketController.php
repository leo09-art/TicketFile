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
    private function statusLabel(string $status): string
    {
        return [
            Ticket::STATUS_EN_ATTENTE => 'En attente',
            Ticket::STATUS_APPELLE => 'Appelé',
            Ticket::STATUS_TRAITE => 'Traité',
            Ticket::STATUS_ABSENT => 'Absent',
            Ticket::STATUS_ANNULE => 'Annulé',
        ][$status] ?? $status;
    }

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

    public function adminDashboardData()
    {
        $stats = [
            'users' => User::count(),
            'tickets' => Ticket::today()->count(),
            'services' => Service::count(),
            'counters' => Counter::count(),
        ];

        $recentTickets = Ticket::with(['service', 'user', 'counter'])
            ->today()
            ->latest()
            ->take(10)
            ->get()
            ->map(function (Ticket $ticket) {
                $duration = ($ticket->called_at && $ticket->treated_at)
                    ? $ticket->called_at->diffInMinutes($ticket->treated_at) . ' min'
                    : null;

                return [
                    'id' => $ticket->id,
                    'ticket_number' => str_pad((string) $ticket->ticket_number, 3, '0', STR_PAD_LEFT),
                    'user' => $ticket->user?->name ?? 'Invité',
                    'service' => $ticket->service?->name ?? '—',
                    'counter' => $ticket->counter?->name ?? '—',
                    'status' => $ticket->status,
                    'status_label' => $this->statusLabel($ticket->status),
                    'created_at' => $ticket->created_at->format('H:i'),
                    'duration' => $duration,
                ];
            })
            ->values();

        $statusTotals = [
            'en_attente' => $recentTickets->where('status', Ticket::STATUS_EN_ATTENTE)->count(),
            'appele' => $recentTickets->where('status', Ticket::STATUS_APPELLE)->count(),
            'traite' => $recentTickets->where('status', Ticket::STATUS_TRAITE)->count(),
            'absent' => $recentTickets->where('status', Ticket::STATUS_ABSENT)->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'status_totals' => $statusTotals,
            'recent_tickets' => $recentTickets,
        ]);
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

    public function agentDashboardData()
    {
        $counter = Counter::where('agent_user_id', Auth::id())->with('service')->first();

        if (!$counter) {
            return response()->json([
                'counter' => null,
                'current_ticket' => null,
                'waiting_tickets' => [],
                'absent_tickets' => [],
                'today_stats' => ['traite' => 0, 'absent' => 0, 'en_attente' => 0],
                'avg_treatment_time' => 0,
            ]);
        }

        $currentTicket = Ticket::where('counter_id', $counter->id)
            ->where('status', Ticket::STATUS_APPELLE)
            ->today()
            ->latest('called_at')
            ->with(['service', 'user'])
            ->first();

        $waitingTickets = Ticket::where('service_id', $counter->service_id)
            ->where('status', Ticket::STATUS_EN_ATTENTE)
            ->today()
            ->orderBy('ticket_number')
            ->with(['service', 'user'])
            ->get();

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
            'traite' => Ticket::where('counter_id', $counter->id)->where('status', Ticket::STATUS_TRAITE)->today()->count(),
            'absent' => Ticket::where('counter_id', $counter->id)->where('status', Ticket::STATUS_ABSENT)->today()->count(),
            'en_attente' => Ticket::where('service_id', $counter->service_id)->where('status', Ticket::STATUS_EN_ATTENTE)->today()->count(),
        ];

        return response()->json([
            'counter' => [
                'id' => $counter->id,
                'name' => $counter->name,
                'service_name' => $counter->service?->name,
            ],
            'current_ticket' => $currentTicket ? [
                'id' => $currentTicket->id,
                'ticket_number' => str_pad((string) $currentTicket->ticket_number, 3, '0', STR_PAD_LEFT),
                'service_name' => $currentTicket->service?->name,
                'user_name' => $currentTicket->user?->name ?? 'Invité',
                'called_at' => $currentTicket->called_at?->format('H:i'),
                'called_at_ts' => $currentTicket->called_at?->timestamp,
                'treated_url' => route('agent.ticket.treated', $currentTicket),
                'absent_url' => route('agent.ticket.absent', $currentTicket),
            ] : null,
            'waiting_tickets' => $waitingTickets->map(fn(Ticket $ticket) => [
                'id' => $ticket->id,
                'ticket_number' => str_pad((string) $ticket->ticket_number, 3, '0', STR_PAD_LEFT),
                'user_name' => $ticket->user?->name ?? 'Invité',
                'created_at' => $ticket->created_at->format('H:i'),
                'wait_minutes' => $ticket->created_at->diffInMinutes(now()),
            ])->values(),
            'absent_tickets' => $absentTickets->map(fn(Ticket $ticket) => [
                'id' => $ticket->id,
                'ticket_number' => str_pad((string) $ticket->ticket_number, 3, '0', STR_PAD_LEFT),
                'user_name' => $ticket->user?->name ?? 'Invité',
                'updated_at' => $ticket->updated_at->format('H:i'),
                'recall_url' => route('agent.ticket.recall', $ticket),
            ])->values(),
            'today_stats' => $todayStats,
            'avg_treatment_time' => $avgTreatmentTime,
        ]);
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
            ->map(function (Service $s) {
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

    public function usagerDashboardData()
    {
        $serviceIdsWithCounter = Counter::whereNotNull('agent_user_id')->pluck('service_id')->unique();

        $services = Service::active()
            ->whereIn('id', $serviceIdsWithCounter)
            ->get()
            ->map(function (Service $s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'description' => $s->description,
                    'waiting' => $s->tickets()->where('status', Ticket::STATUS_EN_ATTENTE)->today()->count(),
                ];
            })
            ->values();

        $myTicket = Ticket::where('user_id', Auth::id())
            ->today()
            ->whereIn('status', [Ticket::STATUS_EN_ATTENTE, Ticket::STATUS_APPELLE])
            ->with('service')
            ->latest()
            ->first();

        return response()->json([
            'services' => $services,
            'my_ticket' => $myTicket ? [
                'id' => $myTicket->id,
                'ticket_number' => str_pad((string) $myTicket->ticket_number, 3, '0', STR_PAD_LEFT),
                'service_name' => $myTicket->service?->name,
                'status' => $myTicket->status,
                'status_label' => $this->statusLabel($myTicket->status),
                'tracking_url' => route('usager.ticket', $myTicket->id),
            ] : null,
        ]);
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
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load('service', 'counter');
        $position = $ticket->getPositionInQueue();
        $estimatedTime = $position * 5;
        return view('pages.users.ticket-suivi', compact('ticket', 'position', 'estimatedTime'));
    }

    public function ticketSuiviData(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load('service', 'counter');
        $position = $ticket->getPositionInQueue();
        $estimatedTime = $position * 5;

        return response()->json([
            'ticket_number' => str_pad((string) $ticket->ticket_number, 3, '0', STR_PAD_LEFT),
            'service_name' => $ticket->service?->name,
            'counter_name' => $ticket->counter?->name,
            'status' => $ticket->status,
            'status_label' => $this->statusLabel($ticket->status),
            'position' => $position,
            'estimated_time' => $estimatedTime,
            'can_cancel' => $ticket->status === Ticket::STATUS_EN_ATTENTE && $ticket->user_id === Auth::id(),
        ]);
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
