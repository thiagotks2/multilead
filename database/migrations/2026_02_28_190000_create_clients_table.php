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
        Schema::create('clients', function (Blueprint $column) {
            $column->id();
            $column->string('name');
            $column->string('email')->index();
            $column->string('phone')->nullable()->index();
            $column->text('notes')->nullable();

            $column->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $column->foreignId('company_id')->constrained()->cascadeOnDelete();

            $column->json('address')->nullable();
            $column->json('profile_data')->nullable();

            $column->timestamps();
            $column->softDeletes();

            $column->unique(['email', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
