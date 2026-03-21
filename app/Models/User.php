<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Colonnes autorisées à l'assignation en masse
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'role',
    ];

    /*
    |--------------------------------------------------------------------------
    | Colonnes cachées lors de la sérialisation (JSON / toArray)
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Conversions de types automatiques
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Normalise le nom : première lettre en majuscule, reste en minuscule.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst(strtolower($value)),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    /**
     * Normalise le prénom de la même façon.
     */
    protected function prenom(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst(strtolower($value)),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    /**
     * Normalise l'email en minuscules.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtolower($value),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    /**
     * Retourne le nom complet du membre (prénom + nom).
     * Propriété calculée, pas stockée en base.
     */
    protected function nomComplet(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst($this->prenom) . ' ' . strtoupper($this->name),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Méthodes utilitaires
    |--------------------------------------------------------------------------
    */

    /**
     * Indique si le membre est administrateur.
     */
    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Indique si le membre a une réservation active en ce moment.
     */
    public function aUneReservationActive(): bool
    {
        return $this->reservations()
            ->whereNull('fin_reservation')
            ->orWhere('fin_reservation', '>', now())
            ->exists();
    }

    /**
     * Indique si le membre est en file d'attente.
     */
    public function estEnFileAttente(): bool
    {
        return $this->voitures()
            ->whereNotNull('place_attente')
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Un membre possède plusieurs voitures.
     */
    public function voitures(): HasMany
    {
        return $this->hasMany(Voiture::class);
    }

    /**
     * Un membre peut avoir plusieurs réservations (historique inclus).
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Réservation active du membre (au maximum une à la fois).
     */
    public function reservationActive(): HasMany
    {
        return $this->hasMany(Reservation::class)
            ->where(function ($query) {
                $query->whereNull('fin_reservation')
                      ->orWhere('fin_reservation', '>', now());
            });
    }
}
