<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First we add the new columns
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('pickup_method', ['delivery', 'pickup'])->default('delivery')->after('total_amount');
            $table->timestamp('pickup_deadline')->nullable()->after('pickup_method');
        });
        
        // Since we are using SQLite and Laravel has limitations with modifying ENUMs via doctrine/dbal,
        // we can't easily alter the 'status' column to add 'menunggu_diambil'.
        // However, in Laravel 11+, sqlite uses native string for ENUMs so it might just work if we treat it as string,
        // or we don't strictly enforce DB level enum in SQLite. 
        // In this project, status is checked in PHP anyway. We don't need a DB-level schema change for the enum values in SQLite.
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_method', 'pickup_deadline']);
        });
    }
};
