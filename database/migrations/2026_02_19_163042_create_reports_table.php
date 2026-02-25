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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->string('title_ar');
            $table->string('title_en')->nullable();

            $table->text('summary_ar')->nullable();
            $table->text('summary_en')->nullable();

            $table->string('period_month')->nullable(); // 01..12
            $table->string('period_year')->nullable();  // 2026

            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();

            $table->string('pdf_path'); // storage path
            $table->boolean('is_public')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['period_year', 'period_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
