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
        Schema::table('standalone_bats', function (Blueprint $table) {
            $table->string('format')->nullable()->after('description');
            $table->string('grammage')->nullable()->after('format');
            $table->decimal('price', 10, 2)->nullable()->after('grammage');
            $table->string('delivery_time')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standalone_bats', function (Blueprint $table) {
            $table->dropColumn(['format', 'grammage', 'price', 'delivery_time']);
        });
    }
};
