<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set email_verified_at untuk semua user lama yang:
        // 1. Admin (role = admin) — admin selalu dianggap terverifikasi
        // 2. User yang sudah approved — user lama sebelum fitur verifikasi ditambahkan
        DB::table('users')
            ->whereNull('email_verified_at')
            ->where(function ($query) {
                $query->where('role', 'admin')
                      ->orWhere('status', 'approved');
            })
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Tidak perlu rollback — tidak aman menghapus verified_at massal
    }
};