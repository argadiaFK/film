<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('download_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('film_id');
            $table->string('name');
            $table->text('url');
            $table->string('quality')->nullable(); // 480p, 720p, 1080p, 4K
            $table->string('size')->nullable(); // e.g. "1.2 GB"
            $table->unsignedBigInteger('click_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('cascade');

            $table->index('film_id');
            $table->index('is_active');
            $table->index('click_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_links');
    }
};
