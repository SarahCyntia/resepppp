<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Langkah extends Model
{
    protected $table = 'langkah';
    protected $fillable = ['resep_id', 'deskripsi', 'urutan'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }
}