<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.u
     */
    public function up(): void
    {
        Schema::create('resep_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resep_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->unique(['resep_id', 'tag_id']);
            $table->foreign('resep_id')->references('id')->on('resep')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tag')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_tag');
    }
};
