<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_recommendations', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('recommendation_type');
            $table->text('error_message')->nullable()->after('reason');
            $table->json('metadata')->nullable()->after('error_message');
        });
    }

    public function down(): void
    {
        Schema::table('ai_recommendations', function (Blueprint $table) {
            $table->dropColumn(['status', 'error_message', 'metadata']);
        });
    }
};
