<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function create()
    {
        $services = Service::active()->get();
        return view('tickets.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

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