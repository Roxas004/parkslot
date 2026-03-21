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

}
