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
        Schema::create('standalone_bat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standalone_bat_id')->constrained()->onDelete('cascade');
            $table->string('event'); // created, sent, validated, refused, modifications_requested, file_updated, token_regenerated
            $table->text('comment')->nullable(); // Client comment or description
            $table->string('old_file_name')->nullable(); // For file updates
            $table->string('new_file_name')->nullable(); // For file updates
            $table->string('actor_type')->default('system'); // system, staff, client
            $table->string('actor_name')->nullable(); // Name of who did the action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standalone_bat_logs');
    }
};
