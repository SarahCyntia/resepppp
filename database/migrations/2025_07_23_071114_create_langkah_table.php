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
        Schema::create('langkah', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('resep_id');
    $table->text('deskripsi');
    $table->unsignedInteger('urutan');
    $table->timestamps();

    $table->foreign('resep_id')->references('id')->on('resep')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langkah');
    }
};
