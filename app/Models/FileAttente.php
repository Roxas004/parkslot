<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileAttente extends Model
{
    use HasFactory;

    protected $table = 'file_attente';

    protected $fillable = [
        'voiture_id',
        'parking_id',
        'position',
    ];
}
