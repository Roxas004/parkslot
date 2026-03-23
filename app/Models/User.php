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

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'string',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isUser()
    {return $this->role==='user';}
    public function voitures(): HasMany
    {
        return $this->hasMany(Voiture::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationActive(): HasMany
    {
        return $this->hasMany(Reservation::class)
            ->where(function ($query) {
                $query->whereNull('fin_reservation')
                      ->orWhere('fin_reservation', '>', now());
            });
    }
}
