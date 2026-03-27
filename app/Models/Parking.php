<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Parking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ville_parking',
        'lib_parking',
        'duree_reservation_defaut',
    ];

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }
}
