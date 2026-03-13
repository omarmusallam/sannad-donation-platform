<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) وسّع enum مؤقتًا
        DB::statement("
            ALTER TABLE donations
            MODIFY payment_method ENUM('manual', 'mock', 'card', 'usdt_trc20') NOT NULL DEFAULT 'mock'
        ");

        // 2) حوّل البيانات القديمة
        DB::table('donations')
            ->where('payment_method', 'mock')
            ->update([
                'payment_method' => 'card',
                'provider' => DB::raw("IFNULL(provider, 'stripe')")
            ]);

        DB::table('donations')
            ->where('payment_method', 'manual')
            ->update([
                'payment_method' => 'usdt_trc20',
                'provider' => DB::raw("IFNULL(provider, 'wallet')")
            ]);

        // 3) ضيّق enum النهائي
        DB::statement("
            ALTER TABLE donations
            MODIFY payment_method ENUM('card', 'usdt_trc20') NOT NULL DEFAULT 'card'
        ");
    }

    public function down(): void
    {
        // 1) وسّع enum مؤقتًا بالعكس
        DB::statement("
            ALTER TABLE donations
            MODIFY payment_method ENUM('manual', 'mock', 'card', 'usdt_trc20') NOT NULL DEFAULT 'mock'
        ");

        // 2) أرجع البيانات
        DB::table('donations')
            ->where('payment_method', 'card')
            ->update(['payment_method' => 'mock']);

        DB::table('donations')
            ->where('payment_method', 'usdt_trc20')
            ->update(['payment_method' => 'manual']);

        // 3) أرجع enum القديم
        DB::statement("
            ALTER TABLE donations
            MODIFY payment_method ENUM('manual', 'mock') NOT NULL DEFAULT 'mock'
        ");
    }
};
