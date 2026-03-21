<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Parking extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Colonnes autorisées à l'assignation en masse
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'ville_parking',
        'lib_parking',
        'duree_reservation_defaut',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Normalise la ville avec une majuscule en début.
     */
    protected function villeParking(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst(strtolower($value)),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    /**
     * Retourne la durée par défaut formatée en heures/minutes lisibles.
     * Ex : 480 minutes → "8h00"
     */
    protected function dureeFormatee(): Attribute
    {
        return Attribute::make(
            get: function () {
                $heures  = intdiv($this->duree_reservation_defaut, 60);
                $minutes = $this->duree_reservation_defaut % 60;
                return sprintf('%dh%02d', $heures, $minutes);
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne le nombre de places disponibles dans ce parking.
     */
    public function nombrePlacesDisponibles(): int
    {
        return $this->places()->where('disponible', true)->count();
    }

    /**
     * Indique si au moins une place est libre.
     */
    public function aDesPlacesDisponibles(): bool
    {
        return $this->nombrePlacesDisponibles() > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Un parking contient plusieurs places (relation Appartenir du MCD).
     */
    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }

    /**
     * Un parking a plusieurs voitures en file d'attente (relation Cif du MCD).
     */
    public function voituresEnAttente(): HasMany
    {
        return $this->hasMany(Voiture::class)
            ->whereNotNull('place_attente')
            ->orderBy('place_attente');
    }
}
