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
        Schema::create('kategori_resep', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resep_id');
            $table->unsignedBigInteger('kategori_id');
            $table->timestamps();

            $table->unique(['resep_id', 'kategori_id']);
            $table->foreign('resep_id')->references('id')->on('resep')->onDelete('cascade');
            $table->foreign('kategori_id')->references('kategori_id')->on('kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_resep');
    }
};
