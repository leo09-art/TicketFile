<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'service_id',
        'user_id',
        'counter_id',
        'status',
        'called_at',
        'treated_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'treated_at' => 'datetime',
    ];

    public const STATUS_EN_ATTENTE = 'en_attente';
    public const STATUS_APPELLE = 'appele';
    public const STATUS_TRAITE = 'traite';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_ANNULE = 'annule';

    public const STATUSES = [
        self::STATUS_EN_ATTENTE,
        self::STATUS_APPELLE,
        self::STATUS_TRAITE,
        self::STATUS_ABSENT,
        self::STATUS_ANNULE,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_EN_ATTENTE);
    }

    public function scopeCalled($query)
    {
        return $query->where('status', self::STATUS_APPELLE);
    }

    public function scopeTreated($query)
    {
        return $query->where('status', self::STATUS_TRAITE);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    public static function generateTicketNumber(): int
    {
        $today = now()->toDateString();

        $lastTicket = self::whereDate('created_at', $today)
            ->orderBy('ticket_number', 'desc')
            ->first();

        return $lastTicket ? $lastTicket->ticket_number + 1 : 1;
    }

    public function getPositionInQueue(): int
    {
        return self::where('service_id', $this->service_id)
            ->where('status', self::STATUS_EN_ATTENTE)
            ->where('created_at', '<=', $this->created_at)
            ->count();
    }
}