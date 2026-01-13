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
        Schema::create('social_media_metrics', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['facebook', 'instagram']);
            $table->string('page_id');
            $table->date('date');
            $table->string('metric_name');
            $table->string('metric_period')->default('day');
            $table->decimal('value', 15, 2)->default(0);
            $table->json('breakdown')->nullable();
            $table->timestamps();

            $table->unique(
                ['platform', 'page_id', 'date', 'metric_name', 'metric_period'],
                'unique_metric'
            );
            $table->index(['platform', 'date']);
            $table->index(['metric_name', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_metrics');
    }
};
