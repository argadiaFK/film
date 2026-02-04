<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slot'); // header_banner, sidebar_top, before_player, etc.
            $table->enum('type', ['image', 'script', 'html'])->default('script');
            $table->text('content'); // Image URL, script code, or HTML
            $table->string('link')->nullable(); // Click URL for image ads
            $table->string('target')->default('_blank'); // _blank, _self
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('slot');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
