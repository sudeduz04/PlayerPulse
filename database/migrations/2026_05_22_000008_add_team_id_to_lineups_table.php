<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lineups', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('match_id')->constrained('teams')->nullOnDelete();
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::table('lineups', function (Blueprint $table) {
            $table->dropIndex(['team_id']);
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
