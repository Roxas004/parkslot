<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileAttente extends Model
{
    use HasFactory;

    protected $table = 'file_attente';

    protected $fillable = [
        'voiture_id',
        'parking_id',
        'position',
        'user_id',
    ];

    public function voiture()
    {
        return $this->belongsTo(Voiture::class, 'voiture_id');
    }

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
