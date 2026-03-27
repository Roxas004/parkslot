<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }
}
