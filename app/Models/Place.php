<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Place extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Colonnes autorisées à l'assignation en masse
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'num_place',
        'disponible',
        'parking_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Conversions de types automatiques
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'disponible' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Normalise le numéro de place en majuscules.
     * Ex : "a1" → "A1"
     */
    protected function numPlace(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtoupper($value),
            set: fn (string $value) => strtoupper(trim($value)),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Marque la place comme occupée.
     */
    public function occuper(): void
    {
        $this->disponible = false;
        $this->save();
    }

    /**
     * Marque la place comme disponible.
     */
    public function liberer(): void
    {
        $this->disponible = true;
        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Une place appartient à un parking (relation Appartenir du MCD).
     */
    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class);
    }

    /**
     * Une place peut avoir plusieurs réservations (historique).
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Réservation actuellement active sur cette place.
     */
    public function reservationActive(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->where(function ($query) {
                $query->whereNull('fin_reservation')
                      ->orWhere('fin_reservation', '>', now());
            });
    }
}
