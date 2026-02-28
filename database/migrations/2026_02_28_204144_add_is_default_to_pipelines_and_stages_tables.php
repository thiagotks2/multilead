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
        Schema::table('pipelines', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('name')->comment('The primary pipeline for this company');
        });

        Schema::table('pipeline_stages', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('color')->comment('The default entry stage for new leads without a routing rule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_stages', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });

        Schema::table('pipelines', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
