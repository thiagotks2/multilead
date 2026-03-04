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
        Schema::create('site_post_categorizables', function (Blueprint $table) {
            $table->foreignId('site_post_id')->constrained()->cascadeOnDelete();
            $table->morphs('categorizable', 'spc_categorizable_index');
            $table->unique(['site_post_id', 'categorizable_id', 'categorizable_type'], 'spc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_post_categorizables');
    }
};
