<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('title_ar');
            $table->string('title_en')->nullable();

            $table->string('slug')->unique();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->decimal('goal_amount', 14, 2)->default(0);
            $table->decimal('current_amount', 14, 2)->default(0);

            $table->string('currency', 3)->default('USD');

            $table->enum('status', ['draft', 'active', 'paused', 'ended', 'archived'])->default('draft');

            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('priority')->default(0);

            $table->string('cover_image_path')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
