<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Episode streaming sources
        Schema::create('episode_streaming_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('episode_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('url');
            $table->enum('type', ['embed', 'hls', 'dash', 'direct'])->default('embed');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('episode_id');
        });

        // Episode download links
        Schema::create('episode_download_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('episode_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('url');
            $table->string('quality')->nullable();
            $table->string('size')->nullable();
            $table->integer('click_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('episode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episode_download_links');
        Schema::dropIfExists('episode_streaming_sources');
    }
};
