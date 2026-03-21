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

    protected $fillable = [
        'num_place',
        'disponible',
        'parking_id',
    ];

    protected $casts = [
        'disponible' => 'boolean',
    ];

}
