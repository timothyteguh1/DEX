<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\IndonesianPhone;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        $setting = \App\Models\Setting::where('key', 'register_info')->first();

        $registerInfo = $setting ? $setting->value
            : 'Silakan transfer biaya registrasi sebesar Rp 150.000 ke rekening BCA 123456789 a/n Blokpedia, lalu unggah bukti transfer pada form di bawah ini.';

        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session()->put('captcha_result', $num1 + $num2);

        return view('auth.register', compact('registerInfo', 'num1', 'num2'));
    }

    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        session()->forget('captcha_result');

        $proofPath = $request->file('payment_proof')->store('proofs', 'public');

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'no_hp'         => $validated['no_hp'],
            'password'      => Hash::make($validated['password']),
            'payment_proof' => $proofPath,
        ]);

        // Kirim email verifikasi
        event(new Registered($user));

        // LOGIN otomatis setelah register agar link verifikasi di email bisa dipakai
        // (Laravel EmailVerificationRequest butuh user sudah login)
        Auth::login($user);

        // Arahkan ke halaman "Cek Email Anda" — BUKAN halaman login
        return redirect()->route('verification.notice')
            ->with('success', 'Pendaftaran berhasil! Silakan cek email Anda dan klik link verifikasi.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Cek verifikasi email — KECUALI admin atau user lama yang sudah approved
        if ($user && !$user->hasVerifiedEmail()) {
            $isAdmin       = $user->isAdmin();
            $isOldApproved = $user->status === User::STATUS_APPROVED;

            if (!$isAdmin && !$isOldApproved) {
                return back()->withErrors([
                    'email' => 'Email Anda belum diverifikasi. Silakan cek kotak masuk (atau folder Spam) email Anda.',
                ])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isApproved()) {
                return redirect()->route('dashboard');
            }

            // Status pending / rejected / failed
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $pesanError = match($user->status) {
                User::STATUS_PENDING => 'Akun Anda sedang ditinjau oleh Admin. Mohon tunggu.',
                User::STATUS_FAILED  => 'Akun Anda gagal karena email tidak diverifikasi tepat waktu. Silakan hubungi Admin.',
                default              => 'Maaf, pendaftaran Anda ditolak. Silakan hubungi Admin.',
            };

            return back()->withErrors(['email' => $pesanError]);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Halaman "Cek Email Anda"
    public function verificationNotice()
    {
        // Kalau sudah verified, arahkan ke login
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')
                ->with('success', 'Email sudah diverifikasi. Silakan login.');
        }

        return view('auth.verify-email');
    }

    // Kirim ulang email verifikasi
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('success', 'Email sudah diverifikasi. Silakan login.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru telah dikirim. Cek juga folder Spam.');
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
            'name'     => ['required', 'string', 'max:255'],
            'no_hp'    => ['required', 'string', 'max:15', new IndonesianPhone],
            'password' => ['nullable', 'min:6', 'confirmed'],
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