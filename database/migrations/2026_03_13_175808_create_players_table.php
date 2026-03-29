<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->restrictOnDelete();

            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->unsignedInteger('jersey_number');
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('dominant_foot');
            $table->string('nationality')->nullable();
            $table->string('status')->default('active');
            $table->string('photo')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['team_id', 'jersey_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
};
