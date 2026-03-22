<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function parking()
    {
        return $this->belongsTo(Parking::class);
    }
}
