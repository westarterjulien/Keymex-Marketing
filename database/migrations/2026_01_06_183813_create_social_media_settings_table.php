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
        Schema::create('social_media_settings', function (Blueprint $table) {
            $table->id();

            // Meta Graph API
            $table->string('meta_access_token', 500)->nullable();
            $table->string('meta_facebook_page_id')->nullable();
            $table->string('meta_instagram_account_id')->nullable();
            $table->string('meta_api_version')->default('v21.0');

            // OpenAI
            $table->string('openai_api_key', 500)->nullable();
            $table->string('openai_model')->default('gpt-4');

            // Status
            $table->boolean('is_active')->default(false);
            $table->timestamp('meta_token_expires_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_settings');
    }
};
