<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignUuid('episode_id')->nullable()->after('series_id')->constrained()->cascadeOnDelete();
        });

        // Migrate existing series comments to the first episode of the series
        $seriesComments = DB::table('comments')->whereNotNull('series_id')->get();
        
        foreach ($seriesComments as $comment) {
            $firstEpisode = DB::table('episodes')
                ->where('series_id', $comment->series_id)
                ->orderBy('season_number')
                ->orderBy('episode_number')
                ->first();
                
            if ($firstEpisode) {
                DB::table('comments')->where('id', $comment->id)->update([
                    'episode_id' => $firstEpisode->id,
                    'series_id' => null, // Optional: clear series_id if you exclusively want it on episode
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['episode_id']);
            $table->dropColumn('episode_id');
        });
    }
};
