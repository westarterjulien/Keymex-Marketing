<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandaloneBatLog extends Model
{
    protected $fillable = [
        'standalone_bat_id',
        'event',
        'comment',
        'old_file_name',
        'new_file_name',
        'actor_type',
        'actor_name',
    ];

    public function bat(): BelongsTo
    {
        return $this->belongsTo(StandaloneBat::class, 'standalone_bat_id');
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created' => 'BAT cree',
            'sent' => 'Envoye pour validation',
            'validated' => 'Valide par le client',
            'refused' => 'Refuse par le client',
            'modifications_requested' => 'Modifications demandees',
            'file_updated' => 'Fichier mis a jour',
            'token_regenerated' => 'Lien regenere',
            'converted_to_order' => 'Converti en commande',
            default => $this->event,
        };
    }

    public function getEventIconAttribute(): string
    {
        return match ($this->event) {
            'created' => 'M12 4v16m8-8H4',
            'sent' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
            'validated' => 'M5 13l4 4L19 7',
            'refused' => 'M6 18L18 6M6 6l12 12',
            'modifications_requested' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'file_updated' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
            'token_regenerated' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'converted_to_order' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        };
    }

    public function getEventColorAttribute(): string
    {
        return match ($this->event) {
            'created' => 'gray',
            'sent' => 'blue',
            'validated' => 'green',
            'refused' => 'red',
            'modifications_requested' => 'orange',
            'file_updated' => 'purple',
            'token_regenerated' => 'yellow',
            'converted_to_order' => 'emerald',
            default => 'gray',
        };
    }
}
