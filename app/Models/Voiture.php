<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Voiture extends Model
{
    use HasFactory;

    protected $fillable = [
        'modele_voiture',
        'couleur_voiture',
        'immatriculation',
        'user_id',
        'parking_id',
    ];

    protected $casts = [
        'place_attente' => 'integer',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationActive(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->where(function ($query) {
                $query->whereNull('fin_reservation')
                      ->orWhere('fin_reservation', '>', now());
            });
    }
}
