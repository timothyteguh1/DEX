<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserStatusChanged;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // AUTO-FAILED: User yang belum verifikasi email lebih dari 1 JAM
        // Email ngawur = tidak bisa klik link = otomatis gagal setelah 1 jam
        User::where('role', 'user')
            ->where('status', User::STATUS_PENDING)
            ->whereNull('email_verified_at')
            ->where('created_at', '<=', now()->subHour())
            ->update(['status' => User::STATUS_FAILED]);

        // --- AWAL LOGIKA FILTER ---
        $query = User::where('role', 'user');

        // Filter Pencarian (Nama, Email, No HP)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Ambil data yang sudah difilter
        $users = $query->orderBy('created_at', 'desc')->get();
        // --- AKHIR LOGIKA FILTER ---

        $registerInfo = \App\Models\Setting::where('key', 'register_info')->first()->value
            ?? 'Silakan transfer biaya registrasi sebesar Rp 150.000 ke rekening BCA 123456789 a/n Blokpedia, lalu unggah bukti transfer pada form di bawah ini.';

        return view('admin.dashboard', compact('users', 'registerInfo'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,failed'
        ]);

        $oldStatus = $user->status;
        $user->update(['status' => $request->status]);

        // --- TAMBAHAN: Catat Log Aktivitas ---
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(), // Admin yang sedang login
            'action' => 'UPDATE_STATUS',
            'description' => auth()->user()->name . ' mengubah status pendaftaran ' . $user->name . ' menjadi ' . strtoupper($request->status),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Logika Kirim Email
        if ($oldStatus !== $user->status) {
            try {
                Mail::to($user->email)->send(new UserStatusChanged($user));
            } catch (\Exception $e) {
                return back()->with('success', 'Status untuk ' . $user->name . ' berhasil diubah, namun gagal mengirim email notifikasi.');
            }
        }

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