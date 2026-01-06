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
        Schema::create('signature_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();

            // Banniere
            $table->string('banner_url')->nullable();
            $table->string('link_url')->nullable();
            $table->string('alt_text')->nullable();

            // Dimensions
            $table->integer('banner_width')->default(750);
            $table->integer('banner_height')->nullable();

            // Periode d'activite
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Statut
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_campaigns');
    }
};
