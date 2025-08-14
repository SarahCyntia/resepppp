<?php
// app/Models/Resep.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

class Resep extends Model
{
    protected $table = 'resep';
    protected $fillable = ['user_id', 'judul', 'deskripsi', 'gambar', 'waktu_masak','kategori_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alat(): HasMany
    {
        return $this->hasMany(Alat::class);
    }

    public function bahan(): HasMany
    {
        return $this->hasMany(Bahan::class);
    }

    public function langkah(): HasMany
    {
        // return $this->hasMany(Langkah::class)->orderBy('urutan');
         return $this->hasMany(Langkah::class);
    }

    // public function kategori(): BelongsToMany
    // {
    //     return $this->belongsToMany(Kategori::class, 'kategori_resep');
    // }

    public function tag(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'resep_tag');
    }

    public function komentar(): HasMany
    {
        return $this->hasMany(Komentar::class);
    }

    public function rating(): HasMany
    {
        return $this->hasMany(RatingResep::class);
    }

    public function favorit(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorit_resep');
    }
    public function averageRating()
{
    return $this->rating()->avg('rating');
}

public function kategori()
{
    return $this->belongsToMany(Kategori::class, 'kategori_resep', 'resep_id', 'kategori_id');
}


public function alatList()
{
    return $this->hasMany(Alat::class);
}

public function bahanList()
{
    return $this->hasMany(Bahan::class);
}

public function langkahList()
{
    return $this->hasMany(Langkah::class);
}


}