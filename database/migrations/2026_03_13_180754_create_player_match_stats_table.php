<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('player_match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();

            $table->unsignedInteger('minutes_played')->default(0);
            $table->boolean('is_starting')->default(false);
            $table->unsignedInteger('goals')->default(0);
            $table->unsignedInteger('assists')->default(0);
            $table->unsignedInteger('shots')->default(0);
            $table->unsignedInteger('successful_passes')->default(0);
            $table->decimal('pass_accuracy', 5, 2)->nullable();
            $table->unsignedInteger('tackles')->default(0);
            $table->unsignedInteger('interceptions')->default(0);
            $table->unsignedInteger('dribbles')->default(0);
            $table->unsignedInteger('fouls')->default(0);
            $table->unsignedInteger('yellow_cards')->default(0);
            $table->unsignedInteger('red_cards')->default(0);
            $table->decimal('match_rating', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['player_id', 'match_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_match_stats');
    }
};
