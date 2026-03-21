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

    /*
    |--------------------------------------------------------------------------
    | Colonnes autorisées à l'assignation en masse
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'modele_voiture',
        'couleur_voiture',
        'immatriculation',
        'place_attente',
        'user_id',
        'parking_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Conversions de types automatiques
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'place_attente' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Force l'immatriculation en majuscules et supprime les espaces.
     * Ex : " ab-123-cd " → "AB-123-CD"
     */
    protected function immatriculation(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtoupper($value),
            set: fn (string $value) => strtoupper(trim($value)),
        );
    }

    /**
     * Normalise le modèle avec une majuscule en début.
     */
    protected function modeleVoiture(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst(strtolower($value)),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    /**
     * Indique si la voiture est actuellement en file d'attente.
     * Propriété calculée, non stockée en base.
     */
    protected function estEnAttente(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->place_attente !== null,
        );
    }

    /**
     * Retourne une description lisible de la voiture.
     * Ex : "Renault Clio grise — AB-123-CD"
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn () => sprintf(
                '%s %s — %s',
                ucfirst($this->modele_voiture),
                strtolower($this->couleur_voiture),
                strtoupper($this->immatriculation)
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Place la voiture en file d'attente à la dernière position.
     */
    public function mettreEnAttente(Parking $parking): void
    {
        $dernierePosition = $parking->voituresEnAttente()->max('place_attente') ?? 0;

        $this->place_attente = $dernierePosition + 1;
        $this->parking_id    = $parking->id;
        $this->save();
    }

    /**
     * Retire la voiture de la file d'attente.
     */
    public function retirerDeLaFileAttente(): void
    {
        $this->place_attente = null;
        $this->parking_id    = null;
        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Une voiture appartient à un membre (relation Posseder du MCD).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une voiture peut être en attente pour un parking (relation Cif du MCD).
     */
    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class);
    }

    /**
     * Une voiture peut avoir plusieurs réservations (historique).
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Réservation active de la voiture.
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
