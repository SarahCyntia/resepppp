<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class RatingResep extends Model
{
    protected $table = 'rating_resep';
    protected $fillable = ['user_id', 'resep_id', 'rating'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}