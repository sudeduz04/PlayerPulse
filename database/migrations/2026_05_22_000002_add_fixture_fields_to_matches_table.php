<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('league_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->unsignedInteger('week')->nullable()->after('league_id');
            $table->foreignId('home_team_id')->nullable()->after('team_id')->constrained('teams')->nullOnDelete();
            $table->foreignId('away_team_id')->nullable()->after('home_team_id')->constrained('teams')->nullOnDelete();
            $table->string('fixture_source')->nullable()->after('match_type');

            $table->index(['league_id', 'week']);
            $table->unique(['league_id', 'week', 'home_team_id', 'away_team_id'], 'matches_fixture_unique');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropUnique('matches_fixture_unique');
            $table->dropIndex(['league_id', 'week']);
            $table->dropConstrainedForeignId('league_id');
            $table->dropConstrainedForeignId('home_team_id');
            $table->dropConstrainedForeignId('away_team_id');
            $table->dropColumn(['week', 'fixture_source']);
        });
    }
};
