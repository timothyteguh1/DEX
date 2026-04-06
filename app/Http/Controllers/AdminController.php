<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ReferralCode;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserStatusChanged;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // AUTO-FAILED: User yang belum verifikasi email lebih dari 1 JAM
        User::where('role', 'user')
            ->where('status', User::STATUS_PENDING)
            ->whereNull('email_verified_at')
            ->where('created_at', '<=', now()->subHour())
            ->update(['status' => User::STATUS_FAILED]);

        // --- LOGIKA FILTER ---
        $query = User::where('role', 'user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

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

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_STATUS',
            'description' => auth()->user()->name . ' mengubah status pendaftaran ' . $user->name . ' menjadi ' . strtoupper($request->status),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

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

    // =========================================================
    // REFERRAL CODE MANAGEMENT (STEP 4)
    // =========================================================

    public function referralCodes()
    {
        $codes = ReferralCode::with('creator')
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.referral-codes', compact('codes'));
    }

    public function storeReferralCode(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:50|unique:referral_codes,code|alpha_dash',
            'description' => 'nullable|string|max:255',
        ]);

        $ref = ReferralCode::create([
            'code'        => strtoupper($request->code),
            'description' => $request->description,
            'created_by'  => auth()->id(),
            'is_active'   => true,
        ]);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'CREATE_REFERRAL',
            'description' => auth()->user()->name . ' membuat kode referral: ' . $ref->code,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Kode referral {$ref->code} berhasil dibuat!");
    }

    public function toggleReferralCode(ReferralCode $referralCode)
    {
        $referralCode->update(['is_active' => !$referralCode->is_active]);
        $status = $referralCode->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'TOGGLE_REFERRAL',
            'description' => auth()->user()->name . ' ' . $status . ' kode referral: ' . $referralCode->code,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Kode referral {$referralCode->code} berhasil {$status}!");
    }

    public function destroyReferralCode(ReferralCode $referralCode)
    {
        $code = $referralCode->code;
        $referralCode->delete();

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'DELETE_REFERRAL',
            'description' => auth()->user()->name . ' menghapus kode referral: ' . $code,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Kode referral {$code} berhasil dihapus!");
    }

    // =========================================================
    // LAPORAN MEMBER (STEP 5)
    // =========================================================

    public function reports(Request $request)
    {
        $year  = $request->input('year', date('Y'));
        $month = $request->input('month'); // null = semua bulan

        $query = User::where('role', User::ROLE_USER);
        $query->whereYear('created_at', $year);

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Statistik
        $totalMembers    = $users->count();
        $withReferral    = $users->filter(fn($u) => !empty($u->referral_code))->count();
        $withoutReferral = $totalMembers - $withReferral;

        // Breakdown per kode referral
        $referralBreakdown = $users->filter(fn($u) => !empty($u->referral_code))
            ->groupBy('referral_code')
            ->map(fn($group, $code) => [
                'code'  => $code,
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();

        // Breakdown per bulan (jika tidak filter bulan tertentu)
        $monthlyBreakdown = [];
        if (!$month) {
            for ($m = 1; $m <= 12; $m++) {
                $mu = $users->filter(fn($u) => $u->created_at->month === $m);
                $muRef = $mu->filter(fn($u) => !empty($u->referral_code))->count();
                $monthlyBreakdown[$m] = [
                    'month_name'       => Carbon::create()->month($m)->translatedFormat('F'),
                    'total'            => $mu->count(),
                    'with_referral'    => $muRef,
                    'without_referral' => $mu->count() - $muRef,
                ];
            }
        }

        // Daftar user detail (untuk tabel)
        $detailUsers = $users;

        // Dropdown tahun
        $availableYears = User::where('role', User::ROLE_USER)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        return view('admin.reports', compact(
            'detailUsers', 'year', 'month',
            'totalMembers', 'withReferral', 'withoutReferral',
            'referralBreakdown', 'monthlyBreakdown', 'availableYears'
        ));
    }
}