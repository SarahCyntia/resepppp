<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('resep', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('judul');
    $table->text('deskripsi')->nullable();
    $table->string('gambar')->nullable(); // path gambar
    $table->string('waktu_masak')->nullable(); // dalam menit
    $table->unsignedBigInteger('kategori_id'); // foreign key ke kategori
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
