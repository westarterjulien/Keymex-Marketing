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
        Schema::create('saved_signatures', function (Blueprint $table) {
            $table->id();

            // Identifiant unique du conseiller (email O365)
            $table->string('advisor_email')->unique();
            $table->string('advisor_mongo_id')->nullable(); // ID MongoDB pour reference

            // Template et marque utilises
            $table->foreignId('signature_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');

            // Donnees personnalisees du conseiller (peuvent overrider MongoDB)
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('job_title')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('picture_url')->nullable(); // URL photo personnalisee
            $table->string('linkedin_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();

            // Signature HTML generee (cache)
            $table->longText('cached_html')->nullable();
            $table->timestamp('cached_at')->nullable();

            $table->timestamps();

            // Index
            $table->index('advisor_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_signatures');
    }
};
