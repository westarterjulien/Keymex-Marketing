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
        Schema::create('sso_group_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('sso_group_id')->unique();
            $table->string('sso_group_name');
            $table->string('sso_group_description')->nullable();
            $table->string('local_role')->nullable(); // super-admin, admin, editor, viewer
            $table->integer('priority')->default(0); // Plus élevé = plus prioritaire
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_group_mappings');
    }
};
