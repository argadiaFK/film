<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Series-Genre pivot
        Schema::create('series_genre', function (Blueprint $table) {
            $table->foreignUuid('series_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('genre_id')->constrained()->cascadeOnDelete();
            $table->primary(['series_id', 'genre_id']);
        });

        // Series-Country pivot
        Schema::create('series_country', function (Blueprint $table) {
            $table->foreignUuid('series_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('country_id')->constrained()->cascadeOnDelete();
            $table->primary(['series_id', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series_country');
        Schema::dropIfExists('series_genre');
    }
};
