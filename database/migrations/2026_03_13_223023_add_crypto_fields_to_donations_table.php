<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('crypto_network')->nullable()->after('provider_ref');
            $table->string('crypto_wallet_address')->nullable()->after('crypto_network');
            $table->string('crypto_tx_hash')->nullable()->after('crypto_wallet_address');
            $table->string('crypto_sender_wallet')->nullable()->after('crypto_tx_hash');
            $table->timestamp('crypto_submitted_at')->nullable()->after('crypto_sender_wallet');
            $table->text('admin_payment_note')->nullable()->after('crypto_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'crypto_network',
                'crypto_wallet_address',
                'crypto_tx_hash',
                'crypto_sender_wallet',
                'crypto_submitted_at',
                'admin_payment_note',
            ]);
        });
    }
};
