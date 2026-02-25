<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaign_updates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();

            $table->string('title_ar');
            $table->string('title_en')->nullable();

            $table->text('body_ar')->nullable();
            $table->text('body_en')->nullable();

            $table->boolean('is_public')->default(true);
            $table->timestamp('published_at')->nullable(); // لو بدك جدولة لاحقًا

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['campaign_id', 'is_public']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_updates');
    }
};
