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

            // بيانات المتبرع (حد أدنى)
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->boolean('is_anonymous')->default(false);

            // المبلغ
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3)->default('USD');

            // الدفع (الآن Mock / لاحقًا Gateway)
            $table->enum('payment_method', ['manual', 'mock'])->default('mock');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            // مرجع خارجي (بوابة دفع لاحقًا)
            $table->string('provider')->nullable();      // stripe / paytabs...
            $table->string('provider_ref')->nullable();  // payment_intent_id / transaction_id

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
