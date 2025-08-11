<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Tag extends Model
{
    protected $table = 'tag';
    protected $fillable = ['nama'];

    public function resep(): BelongsToMany
    {
        return $this->belongsToMany(Resep::class, 'resep_tag');
    }
}