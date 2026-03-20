<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus aturan unik lama yang bersifat global
        Schema::table('coins', function (Blueprint $table) {
            $table->dropUnique('coins_token_address_unique');
        });

        // 2. Tambah kolom identitas pemilik dan status layar aktif
        Schema::table('coins', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->boolean('is_active')->default(false)->after('pair_address');
        });

        // 3. Selamatkan data koin yang sudah ada, berikan ke Admin (ID 1: deus)
        DB::table('coins')->update(['user_id' => 1]);

        // 4. Kunci keamanannya (Foreign Key & Unique per User)
        Schema::table('coins', function (Blueprint $table) {
            // Jika user dihapus, semua koinnya ikut terhapus otomatis (Cascade)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Satu user tidak bisa nambah koin yang sama 2x, tapi Budi dan Andi boleh punya koin yang sama
            $table->unique(['user_id', 'token_address']);
        });
    }

    public function down(): void
    {
        Schema::table('coins', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'token_address']);
            $table->dropColumn(['user_id', 'is_active']);
            $table->unique('token_address', 'coins_token_address_unique');
        });
    }
};