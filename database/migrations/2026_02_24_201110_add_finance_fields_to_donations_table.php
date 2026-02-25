<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->decimal('fees', 14, 2)->default(0)->after('amount');
            $table->decimal('net_amount', 14, 2)->nullable()->after('fees');
            $table->timestamp('refunded_at')->nullable()->after('paid_at');

            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['paid_at']);
            $table->dropColumn(['fees', 'net_amount', 'refunded_at']);
        });
    }
};
