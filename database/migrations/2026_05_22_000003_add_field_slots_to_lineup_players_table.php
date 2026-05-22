<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lineup_players', function (Blueprint $table) {
            $table->string('slot_key')->nullable()->after('position_id');
            $table->unsignedTinyInteger('field_x')->nullable()->after('slot_key');
            $table->unsignedTinyInteger('field_y')->nullable()->after('field_x');
        });
    }

    public function down(): void
    {
        Schema::table('lineup_players', function (Blueprint $table) {
            $table->dropColumn(['slot_key', 'field_x', 'field_y']);
        });
    }
};
