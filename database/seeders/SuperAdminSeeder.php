<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@blockped.com'], // Cek agar tidak duplikat jika dijalankan 2x
            [
                'name' => 'Pemilik Sistem',
                'no_hp' => '081234567890',
                'password' => Hash::make('SuperRahasia123!'), // Ganti dengan password yang kuat
                'role' => User::ROLE_SUPERADMIN,
                'status' => User::STATUS_APPROVED,
                'email_verified_at' => now(),
            ]
        );
    }
}