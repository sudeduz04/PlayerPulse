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
        Schema::create('player_training_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_id')->constrained()->cascadeOnDelete();

            $table->string('attendance_status')->default('attended');
            $table->decimal('performance_score', 5, 2)->nullable();
            $table->decimal('speed_score', 5, 2)->nullable();
            $table->decimal('endurance_score', 5, 2)->nullable();
            $table->decimal('technique_score', 5, 2)->nullable();
            $table->decimal('discipline_score', 5, 2)->nullable();
            $table->text('coach_comment')->nullable();

            $table->timestamps();

            $table->unique(['player_id', 'training_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_training_performances');
    }
};
