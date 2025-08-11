<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Komentar extends Model
{
    protected $table = 'komentar';
    protected $fillable = ['user_id', 'resep_id', 'isi'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}