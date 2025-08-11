<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Bahan extends Model
{
    protected $table = 'bahan';
    protected $fillable = ['resep_id', 'nama', 'jumlah'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }
}