<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('film_country', function (Blueprint $table) {
            $table->uuid('film_id');
            $table->uuid('country_id');

            $table->primary(['film_id', 'country_id']);

            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('cascade');

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('film_country');
    }
};
