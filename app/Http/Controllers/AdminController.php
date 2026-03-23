<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // AUTO-FAILED: User yang belum verifikasi email lebih dari 1 JAM
        // Email ngawur = tidak bisa klik link = otomatis gagal setelah 1 jam
        User::where('role', 'user')
            ->where('status', User::STATUS_PENDING)
            ->whereNull('email_verified_at')
            ->where('created_at', '<=', now()->subHour())
            ->update(['status' => User::STATUS_FAILED]);

        $users = User::where('role', 'user')->orderBy('created_at', 'desc')->get();

        $registerInfo = \App\Models\Setting::where('key', 'register_info')->first()->value
            ?? 'Silakan transfer biaya registrasi sebesar Rp 150.000 ke rekening BCA 123456789 a/n Blokpedia, lalu unggah bukti transfer pada form di bawah ini.';

        return view('admin.dashboard', compact('users', 'registerInfo'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,failed'
        ]);

        $user->update(['status' => $request->status]);

        return back()->with('success', 'Status untuk ' . $user->name . ' berhasil diubah menjadi ' . strtoupper($request->status));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'register_info' => 'required|string'
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'register_info'],
            ['value' => $request->register_info]
        );

        return back()->with('success', 'Pengaturan Informasi Pendaftaran berhasil diperbarui!');
    }
}