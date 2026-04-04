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
        Schema::create('site_categorizables', function (Blueprint $table) {
            $table->foreignId('site_category_id')->constrained()->cascadeOnDelete();
            $table->morphs('categorizable', 'sc_categorizable_index');
            $table->unique(['site_category_id', 'categorizable_id', 'categorizable_type'], 'sc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_categorizables');
    }
};
