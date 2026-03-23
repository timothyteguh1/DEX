<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // nullable() dan nullOnDelete() agar jika admin dihapus, log-nya tetap ada sebagai bukti (Standar Audit)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); 
            
            $table->string('action'); // Contoh: 'UPDATE_STATUS', 'DELETE_USER', 'LOGIN'
            $table->text('description'); // Penjelasan detail: "Admin Toti mengubah status Budi menjadi Approved"
            $table->string('ip_address', 45)->nullable(); // Rekam IP Pelaku
            $table->text('user_agent')->nullable(); // Rekam Browser/Device Pelaku
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};