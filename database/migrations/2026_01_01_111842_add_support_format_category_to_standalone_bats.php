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
            // Add foreign keys for support type, format and category
            $table->foreignId('support_type_id')->nullable()->after('advisor_agency')->constrained()->nullOnDelete();
            $table->foreignId('format_id')->nullable()->after('support_type_id')->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('format_id')->constrained()->nullOnDelete();

            // Drop old text column (format is now a foreign key)
            $table->dropColumn('format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standalone_bats', function (Blueprint $table) {
            $table->dropForeign(['support_type_id']);
            $table->dropForeign(['format_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['support_type_id', 'format_id', 'category_id']);
            $table->string('format')->nullable()->after('description');
        });
    }
};
