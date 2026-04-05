<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->uuid('public_id')->nullable()->after('id')->unique();
        });

        DB::table('donations')
            ->select('id')
            ->orderBy('id')
            ->each(function ($donation) {
                DB::table('donations')
                    ->where('id', $donation->id)
                    ->update(['public_id' => (string) Str::uuid()]);
            });

        Schema::table('donations', function (Blueprint $table) {
            $table->uuid('public_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropColumn('public_id');
        });
    }
};
