<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Menampilkan halaman dashboard admin
    public function index()
{
    $users = User::where('role', 'user')->orderBy('created_at', 'desc')->get();
    
    // Ambil teks pengaturan saat ini
    $registerInfo = \App\Models\Setting::where('key', 'register_info')->first()->value 
        ?? 'Silakan transfer biaya registrasi sebesar Rp 150.000 ke rekening BCA 123456789 a/n Blokpedia, lalu unggah bukti transfer pada form di bawah ini.';

    return view('admin.dashboard', compact('users', 'registerInfo'));
}

    // Mengubah status ACC / Reject
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $user->update(['status' => $request->status]);

        return back()->with('success', 'Status untuk ' . $user->name . ' berhasil diubah menjadi ' . strtoupper($request->status));
    }
    public function updateSettings(Request $request)
{
    $request->validate([
        'register_info' => 'required|string'
    ]);

    // Simpan atau update ke database
    \App\Models\Setting::updateOrCreate(
        ['key' => 'register_info'],
        ['value' => $request->register_info]
    );

    return back()->with('success', 'Pengaturan Informasi Pendaftaran berhasil diperbarui!');
}


}