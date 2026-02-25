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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();

            $table->string('receipt_no')->unique();
            $table->uuid('uuid')->unique();

            $table->foreignId('donation_id')->constrained()->cascadeOnDelete();

            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();

            $table->decimal('amount', 14, 2);
            $table->string('currency', 3);

            $table->enum('status', ['issued', 'void'])->default('issued');

            $table->string('pdf_path')->nullable();

            $table->timestamp('issued_at');
            $table->timestamp('email_last_sent_at')->nullable();
            $table->unsignedInteger('email_sent_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
