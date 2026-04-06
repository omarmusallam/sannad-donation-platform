<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE donations
            MODIFY status ENUM(
                'pending',
                'pending_crypto_review',
                'paid',
                'failed',
                'refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("
            ALTER TABLE donations
            MODIFY status ENUM(
                'pending',
                'paid',
                'failed',
                'refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
