<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('streaming_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('film_id');
            $table->string('name');
            $table->text('url');
            $table->string('type')->default('embed'); // embed, iframe, direct
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('film_id')
                ->references('id')
                ->on('films')
                ->onDelete('cascade');

            $table->index('film_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streaming_sources');
    }
};
