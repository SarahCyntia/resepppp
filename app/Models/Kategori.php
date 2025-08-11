<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategori';
    // protected $fillable = ['nama'];

    protected $primaryKey = 'kategori_id';
    protected $fillable = [
        'kategori_id',
        'id',
        // 'pengguna_id',
        'nama',
    ];

    // public function resep(): BelongsToMany
    // {
    //     return $this->belongsToMany(Resep::class, 'kategori');
    // }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
    public function getRouteKeyName()
    {
        return 'id';
    }
    public function resep()
{
    return $this->belongsToMany(Resep::class, 'kategori_resep', 'kategori_id', 'resep_id');
}

    // public function kurir()
    // {
    //     return $this->belongsTo(Kurir::class, 'id');
    // }
}

