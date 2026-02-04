<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis')->nullable();
            $table->integer('year')->nullable();
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('trailer_url')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->integer('total_seasons')->default(1);
            $table->integer('total_episodes')->default(0);
            $table->enum('status', ['ongoing', 'completed', 'cancelled', 'draft'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('status');
            $table->index('year');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
