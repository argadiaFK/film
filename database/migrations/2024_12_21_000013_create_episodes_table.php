<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('series_id')->constrained()->cascadeOnDelete();
            $table->integer('season_number')->default(1);
            $table->integer('episode_number');
            $table->string('title');
            $table->string('slug');
            $table->text('synopsis')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('thumbnail')->nullable();
            $table->date('air_date')->nullable();
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamps();

            $table->unique(['series_id', 'season_number', 'episode_number']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
