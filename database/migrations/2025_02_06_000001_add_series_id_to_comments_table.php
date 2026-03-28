<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Make film_id nullable since comments can now be on series
            $table->uuid('film_id')->nullable()->change();

            // Add series_id column
            $table->foreignUuid('series_id')->nullable()->after('film_id')->constrained()->cascadeOnDelete();

            $table->index('series_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropIndex(['series_id']);
            $table->dropColumn('series_id');
        });
    }
};
