<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();

            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->boolean('is_anonymous')->default(false);

            $table->decimal('amount', 14, 2);
            $table->decimal('fees', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            $table->enum('payment_method', ['card', 'usdt_trc20'])->default('card');
            $table->enum('status', ['pending', 'pending_crypto_review', 'paid', 'failed', 'refunded'])->default('pending');

            $table->string('provider')->nullable();
            $table->string('provider_ref')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
