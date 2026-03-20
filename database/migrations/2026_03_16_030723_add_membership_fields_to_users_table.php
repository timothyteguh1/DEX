<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable()->after('email');
            // Role: admin / user
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            // Status: pending / approved / rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('role');
            // Path file bukti pembayaran/follow
            $table->string('payment_proof')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'role', 'status', 'payment_proof']);
        });
    }
};