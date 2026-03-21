<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Colonnes autorisées à l'assignation en masse
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'debut_reservation',
        'fin_reservation',
        'user_id',
        'place_id',
        'voiture_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Conversions de types automatiques
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'debut_reservation' => 'datetime',
        'fin_reservation'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Indique si la réservation est encore active.
     * Active = pas de fin_reservation OU fin_reservation dans le futur.
     */
    protected function estActive(): Attribute
    {
        return Attribute::make(
            get: fn () => is_null($this->fin_reservation)
                || $this->fin_reservation->isFuture(),
        );
    }

    /**
     * Indique si la réservation est expirée.
     */
    protected function estExpiree(): Attribute
    {
        return Attribute::make(
            get: fn () => ! is_null($this->fin_reservation)
                && $this->fin_reservation->isPast(),
        );
    }

    /**
     * Retourne le temps restant avant expiration sous forme lisible.
     * Ex : "dans 2 heures", "expirée"
     */
    protected function tempsRestant(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (is_null($this->fin_reservation)) {
                    return 'Indéfini';
                }

                if ($this->fin_reservation->isPast()) {
                    return 'Expirée';
                }

                return $this->fin_reservation->diffForHumans();
            },
        );
    }

    /**
     * Retourne la durée totale de la réservation en minutes.
     */
    protected function dureeEnMinutes(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fin = $this->fin_reservation ?? now();
                return (int) $this->debut_reservation->diffInMinutes($fin);
            },
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Ferme la réservation immédiatement (fin anticipée).
     * Met aussi la place associée comme disponible.
     */
    public function fermer(): void
    {
        $this->fin_reservation = now();
        $this->save();

        // Libère la place
        $this->place->liberer();

        // Attribue la place au premier de la file d'attente si besoin
        $this->attribuerAuxSuivants();
    }

    /**
     * Crée une réservation immédiate pour un membre.
     * Choisit une place libre aléatoirement dans le parking donné.
     * Si aucune place n'est libre, met la voiture en file d'attente.
     */
    public static function creerOuMettreEnAttente(
        User    $user,
        Voiture $voiture,
        Parking $parking
    ): self|null {
        // Cherche une place libre aléatoirement
        $place = $parking->places()
            ->where('disponible', true)
            ->inRandomOrder()
            ->first();

        if (! $place) {
            // Aucune place libre → file d'attente
            $voiture->mettreEnAttente($parking);
            return null;
        }

        // Calcule la date de fin selon la durée par défaut du parking
        $fin = now()->addMinutes($parking->duree_reservation_defaut);

        // Crée la réservation
        $reservation = self::create([
            'debut_reservation' => now(),
            'fin_reservation'   => $fin,
            'user_id'           => $user->id,
            'place_id'          => $place->id,
            'voiture_id'        => $voiture->id,
        ]);

        // Marque la place comme occupée
        $place->occuper();

        return $reservation;
    }

    /**
     * Après libération d'une place, attribue la place
     * au premier membre de la file d'attente du parking.
     */
    private function attribuerAuxSuivants(): void
    {
        $parking = $this->place->parking;

        // Récupère la première voiture en attente dans ce parking
        $voitureEnAttente = $parking->voituresEnAttente()->first();

        if (! $voitureEnAttente) {
            return;
        }

        // Crée la réservation pour ce membre
        self::creerOuMettreEnAttente(
            $voitureEnAttente->user,
            $voitureEnAttente,
            $parking
        );

        // Retire la voiture de la file d'attente
        $voitureEnAttente->retirerDeLaFileAttente();

        // Réordonne la file d'attente (décale tout le monde d'une position)
        $parking->voituresEnAttente()
            ->each(function (Voiture $voiture) {
                $voiture->decrement('place_attente');
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Une réservation est prise par un membre (relation Prendre du MCD).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une réservation occupe une place (relation Reserver du MCD).
     */
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Une réservation concerne une voiture (relation Concerner du MCD).
     */
    public function voiture(): BelongsTo
    {
        return $this->belongsTo(Voiture::class);
    }
}
