<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login'); // Nanti kita buat UI-nya
    }

    public function showRegister()
    {
        // Cara yang lebih aman (anti-error) untuk mengambil data dari database
        $setting = \App\Models\Setting::where('key', 'register_info')->first();
        
        $registerInfo = $setting ? $setting->value : 'Silakan transfer biaya registrasi sebesar Rp 150.000 ke rekening BCA 123456789 a/n Blokpedia, lalu unggah bukti transfer pada form di bawah ini.';

        // WAJIB ADA compact('registerInfo') agar variabelnya terkirim ke view
        return view('auth.register', compact('registerInfo'));
    }

    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        // 1. Simpan file gambar dengan aman ke folder storage/app/public/proofs
        $proofPath = $request->file('payment_proof')->store('proofs', 'public');

        // 2. Buat User baru (otomatis berstatus 'pending' sesuai default database)
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'payment_proof' => $proofPath,
        ]);

        // 3. Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login untuk melihat status persetujuan Anda.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Cek Alur Login berdasarkan Standar Klien Anda
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isApproved()) {
                return redirect()->route('dashboard'); // Ke terminal DexScreener
            }

            // Jika status masih pending atau rejected
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $pesanError = $user->status === User::STATUS_PENDING 
                ? 'Akun Anda sedang ditinjau oleh Admin. Mohon tunggu.' 
                : 'Maaf, pendaftaran Anda ditolak. Silakan hubungi Admin.';

            return back()->withErrors(['email' => $pesanError]);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    // Fungsi baru untuk Update Profil User
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            // Password opsional, hanya diubah kalau diisi
            'password' => 'nullable|min:6|confirmed', 
        ]);

        // Update data
        $user->name = $validated['name'];
        $user->no_hp = $validated['no_hp'];
        
        // Jika form password diisi, update passwordnya
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
    
    
}