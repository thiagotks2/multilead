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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();

            // Basic Settings
            $table->string('name');
            $table->enum('status', ['development', 'production', 'inactive'])->default('development');

            // Branding & Visuals (JSON)
            $table->json('visual_settings')->nullable();

            // SEO
            $table->string('default_meta_title')->nullable();
            $table->text('default_meta_description')->nullable();
            $table->text('default_meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();

            // Scripts & Tracking
            $table->text('scripts_header')->nullable();
            $table->text('scripts_body')->nullable();
            $table->text('scripts_footer')->nullable();

            // Mail Configuration (SMTP & Defaults)
            $table->string('mail_default_recipient')->nullable();
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();

            // Legal / Text
            $table->longText('privacy_policy_text')->nullable();

            // Relations & Meta
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
