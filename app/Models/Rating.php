<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'rating';

    protected $fillable = [
        'user_id',
        'resep_id',
        'nilai',
    ];

    // Relasi ke user yang memberikan rating
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke resep yang diberi rating
    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }
}
