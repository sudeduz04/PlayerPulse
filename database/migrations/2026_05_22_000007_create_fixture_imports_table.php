<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixture_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_filename')->nullable();
            $table->string('file_path')->nullable();
            $table->string('source')->default('file');
            $table->string('status')->default('queued');
            $table->unsignedInteger('created_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
            $table->json('skipped_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['league_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixture_imports');
    }
};
