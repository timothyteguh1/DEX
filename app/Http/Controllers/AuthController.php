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
        return view('auth.login');
    }

    public function showRegister()
    {
        $setting = \App\Models\Setting::where('key', 'register_info')->first();

        $registerInfo = $setting ? $setting->value : 'Silakan transfer biaya registrasi sebesar Rp 150.000 ke rekening BCA 123456789 a/n Blokpedia, lalu unggah bukti transfer pada form di bawah ini.';

        // Bikin Captcha Manual: Dua angka acak
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $captchaResult = $num1 + $num2;

        // Simpan hasil aslinya di Session untuk dicek oleh ValidCaptcha Rule
        session()->put('captcha_result', $captchaResult);

        return view('auth.register', compact('registerInfo', 'num1', 'num2'));
    }

    public function register(RegisterUserRequest $request)
    {
        // Semua validasi (termasuk captcha) sudah ditangani di RegisterUserRequest
        $validated = $request->validated();

        // Hapus session captcha setelah validasi berhasil agar tidak bisa di-reuse
        session()->forget('captcha_result');

        // 1. Simpan file gambar dengan aman ke folder storage/app/public/proofs
        $proofPath = $request->file('payment_proof')->store('proofs', 'public');

        // 2. Buat User baru (otomatis berstatus 'pending' sesuai default database)
        User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'no_hp'         => $validated['no_hp'],
            'password'      => Hash::make($validated['password']),
            'payment_proof' => $proofPath,
        ]);

        // 3. Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login untuk melihat status persetujuan Anda.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isApproved()) {
                return redirect()->route('dashboard');
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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'no_hp'    => 'required|string|max:20',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name  = $validated['name'];
        $user->no_hp = $validated['no_hp'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}