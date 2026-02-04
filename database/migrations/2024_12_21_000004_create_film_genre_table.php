<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('film_genre', function (Blueprint $table) {
            $table->uuid('film_id');
            $table->uuid('genre_id');

            $table->primary(['film_id', 'genre_id']);

            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('cascade');

            $table->foreign('genre_id')
                ->references('id')
                ->on('genres')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('film_genre');
    }
};
