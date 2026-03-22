<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'debut_reservation',
        'fin_reservation',
        'user_id',
        'place_id',
        'voiture_id',
    ];

    protected $casts = [
        'debut_reservation' => 'datetime',
        'fin_reservation'   => 'datetime',
    ];

    public function getPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function getVoiture(): BelongsTo
    {
        return $this->belongsTo(Voiture::class, 'voiture_id');
    }
}
