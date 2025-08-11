<?php
// database/seeders/ResepSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Resep, Alat, Bahan, Langkah, Kategori, Tag};

class ResepSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Buat Kategori & Tag default
        $kategori = Kategori::firstOrCreate(['nama' => 'Sarapan']);
        $tagCepat = Tag::firstOrCreate(['nama' => 'cepat']);
        $tagMudah = Tag::firstOrCreate(['nama' => 'mudah']);

        $resep = Resep::create([
            'user_id' => $user->id,
            'judul' => 'Nasi Goreng Sederhana',
            'deskripsi' => 'Resep nasi goreng mudah dan cepat untuk sarapan.',
            'gambar' => 'nasi-goreng.jpg',
            'waktu_masak' => '15 menit'
        ]);

        // Relasi kategori dan tag
        $resep->kategori()->attach($kategori->id);
        $resep->tag()->attach([$tagCepat->id, $tagMudah->id]);

        // Alat
        Alat::insert([
            ['resep_id' => $resep->id, 'nama' => 'Wajan'],
            ['resep_id' => $resep->id, 'nama' => 'Spatula'],
        ]);

        // Bahan
        Bahan::insert([
            ['resep_id' => $resep->id, 'nama' => 'Nasi putih', 'jumlah' => '1 piring'],
            ['resep_id' => $resep->id, 'nama' => 'Telur', 'jumlah' => '1 butir'],
            ['resep_id' => $resep->id, 'nama' => 'Kecap manis', 'jumlah' => '1 sdm'],
            ['resep_id' => $resep->id, 'nama' => 'Garam', 'jumlah' => 'secukupnya'],
        ]);

        // Langkah
        Langkah::insert([
            ['resep_id' => $resep->id, 'deskripsi' => 'Panaskan wajan dan tumis telur.', 'urutan' => 1],
            ['resep_id' => $resep->id, 'deskripsi' => 'Masukkan nasi, aduk rata.', 'urutan' => 2],
            ['resep_id' => $resep->id, 'deskripsi' => 'Tambahkan kecap dan garam.', 'urutan' => 3],
            ['resep_id' => $resep->id, 'deskripsi' => 'Aduk rata dan sajikan hangat.', 'urutan' => 4],
        ]);
    }
}
