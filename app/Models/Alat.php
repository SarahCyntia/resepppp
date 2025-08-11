<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Alat extends Model
{
    protected $table = 'alat';
    protected $fillable = ['resep_id', 'nama'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }
}