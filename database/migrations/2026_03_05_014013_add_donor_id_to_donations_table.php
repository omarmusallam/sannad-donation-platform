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
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('donor_id')
                ->nullable()
                ->after('campaign_id')
                ->constrained('donors')
                ->nullOnDelete();

            $table->index('donor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['donor_id']);
            $table->dropIndex(['donor_id']);
            $table->dropColumn('donor_id');
        });
    }
};
