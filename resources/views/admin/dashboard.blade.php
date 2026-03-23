<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blockped</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #05070a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .ds-bg-panel { background-color: #0f131a; }
        .ds-border { border-color: rgba(31, 41, 55, 0.6); }
        .fade-out { opacity: 0; pointer-events: none; transition: opacity 0.4s ease-out; }
    </style>
</head>
<body class="p-4 md:p-8 font-sans relative min-h-screen selection:bg-[#20d981] selection:text-black">

    <div id="loading-screen" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-[#05070a] transition-opacity duration-300">
        <svg class="animate-spin h-12 w-12 text-[#20d981] mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-[#20d981] font-extrabold tracking-widest uppercase text-xs animate-pulse">Memuat Data Panel...</span>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center border-b ds-border pb-6 mb-8">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-[#20d981]/20 border border-[#20d981]/50 flex items-center justify-center text-[#20d981] font-black overflow-hidden shrink-0">
                <img src="{{ asset('logo-blokpedia.png') }}" class="w-full h-full object-cover rounded-full" alt="B"
                    onerror="this.style.display='none'; this.parentNode.innerText='B'">
            </span>
            <div>
                <div class="font-extrabold text-white text-base tracking-wide">
                    ADMIN PANEL BLOCKPED
                    @if(auth()->user()->isSuperAdmin())
                        <span class="ml-2 bg-purple-500/20 text-purple-400 text-[10px] px-2 py-0.5 rounded uppercase tracking-widest border border-purple-500/30">Superadmin</span>
                    @endif
                </div>
                <p class="text-gray-500 font-medium text-xs">Kelola pendaftaran dan status member.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('superadmin.logs') }}" class="bg-purple-600/20 hover:bg-purple-600/40 text-purple-400 hover:text-white border border-purple-500/30 py-2.5 px-6 rounded-xl font-bold transition flex items-center gap-2">
                    📜 Log History
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" onsubmit="showLoading()">
                @csrf
                <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-white border border-red-500/30 py-2.5 px-6 rounded-xl font-bold transition">
                    Logout
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div id="alert-success" class="bg-[#20d981]/10 border border-[#20d981]/30 text-[#20d981] p-4 rounded-2xl font-bold mb-6 flex justify-between items-center">
            <span>✨ {{ session('success') }}</span>
            <button onclick="document.getElementById('alert-success').remove()">✖</button>
        </div>
    @endif
    @if (session('error'))
        <div id="alert-error" class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-2xl font-bold mb-6 flex justify-between items-center">
            <span>⛔ {{ session('error') }}</span>
            <button onclick="document.getElementById('alert-error').remove()">✖</button>
        </div>
    @endif
    @if ($errors->any())
        <div id="alert-error-val" class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-2xl font-bold mb-6 flex justify-between items-center">
            <span>⚠️ {{ $errors->first() }}</span>
            <button onclick="document.getElementById('alert-error-val').remove()">✖</button>
        </div>
    @endif

    {{-- Pengaturan --}}
    <div class="ds-bg-panel ds-border border rounded-3xl p-8 mb-8 shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
        <h2 class="text-xl font-extrabold text-white mb-5 flex items-center gap-2">
            <span class="text-blue-400">⚙️</span> Pengaturan Teks Pendaftaran
        </h2>
        <form action="{{ route('admin.settings.update') }}" method="POST" onsubmit="showLoading()">
            @csrf
            <textarea name="register_info" rows="3" required
                class="w-full bg-[#05070a] text-white border ds-border rounded-xl p-4 outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all mb-4 leading-relaxed">{{ $registerInfo }}</textarea>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-extrabold py-3 px-8 rounded-xl transition text-sm uppercase tracking-widest">
                Simpan Pengaturan
            </button>
        </form>
    </div>

    {{-- FILTER SECTION --}}
    <div class="ds-bg-panel ds-border border rounded-2xl p-4 mb-6 shadow-lg">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama, email, atau no HP..." 
                class="bg-[#05070a] text-white border ds-border rounded-xl p-3 flex-grow focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] outline-none transition-all text-sm font-medium">
            
            <select name="status" class="bg-[#05070a] text-white border ds-border rounded-xl p-3 focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] outline-none transition-all text-sm cursor-pointer md:w-48 font-bold">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>🟡 Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>🟢 Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>🔴 Rejected</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>🟠 Failed</option>
            </select>
            
            <button type="submit" class="bg-[#20d981] hover:bg-[#1bb86d] text-black px-6 py-3 rounded-xl font-extrabold transition shadow-[0_0_15px_rgba(32,217,129,0.2)] text-sm">
                Cari & Filter
            </button>
            <a href="{{ route('admin.dashboard') }}" class="bg-[#1e293b] hover:bg-[#334155] text-white border border-gray-700 px-6 py-3 rounded-xl font-extrabold transition text-sm text-center">
                Reset
            </a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="ds-bg-panel ds-border border rounded-3xl overflow-hidden shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-[#05070a] text-gray-500 border-b ds-border">
                    <tr>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Nama & Kontak</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Email Verified</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Tanggal Daftar</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px] text-center">Bukti</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px] text-center">Status</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse ($users as $user)
                        <tr class="hover:bg-[#1e293b]/30 transition-colors">
                            <td class="p-5">
                                <div class="font-extrabold text-white text-base mb-0.5">
                                    {{ $user->name }}
                                    @if($user->role === 'admin')
                                        <span class="ml-1 bg-blue-500/20 text-blue-400 text-[9px] px-1.5 py-0.5 rounded border border-blue-500/30 uppercase">Admin</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">📧 {{ $user->email }}</div>
                                <div class="text-xs text-gray-500">📱 {{ $user->no_hp }}</div>
                            </td>

                            <td class="p-5">
                                @if ($user->email_verified_at)
                                    <span class="flex items-center gap-1.5 text-[#20d981] text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-[#20d981]"></span> Terverifikasi
                                    </span>
                                    <div class="text-[10px] text-gray-600 mt-0.5">{{ $user->email_verified_at->format('d M Y, H:i') }}</div>
                                @else
                                    <span class="flex items-center gap-1.5 text-red-400 text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span> Belum Verifikasi
                                    </span>
                                    @php $sisaMenit = 60 - (int)$user->created_at->diffInMinutes(now()); @endphp
                                    @if ($sisaMenit > 0)
                                        <div class="text-[10px] text-yellow-500 mt-0.5">⏳ Sisa ~{{ $sisaMenit }} menit</div>
                                    @else
                                        <div class="text-[10px] text-red-500 mt-0.5">⛔ Sudah expired</div>
                                    @endif
                                @endif
                            </td>

                            <td class="p-5 text-gray-400 text-xs font-semibold">
                                {{ $user->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="p-5 text-center">
                                @if ($user->payment_proof)
                                    <button onclick="openModal('{{ asset('storage/' . $user->payment_proof) }}')"
                                        class="bg-blue-500/10 text-blue-400 hover:text-white hover:bg-blue-500/20 border border-blue-500/30 px-3.5 py-2 rounded-lg transition text-xs font-bold">
                                        🖼️ Lihat
                                    </button>
                                @else
                                    <span class="text-red-500/70 text-xs italic">Tidak ada</span>
                                @endif
                            </td>

                            <td class="p-5 text-center">
                                @if ($user->status === 'approved')
                                    <span class="bg-[#20d981]/10 text-[#20d981] border border-[#20d981]/30 px-3 py-1.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest">APPROVED</span>
                                @elseif ($user->status === 'rejected')
                                    <span class="bg-red-500/10 text-red-400 border border-red-500/30 px-3 py-1.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest">REJECTED</span>
                                @elseif ($user->status === 'failed')
                                    <span class="bg-orange-500/10 text-orange-400 border border-orange-500/30 px-3 py-1.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest">FAILED</span>
                                @else
                                    <span class="bg-yellow-500/10 text-yellow-400 border border-yellow-500/30 px-3 py-1.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest">PENDING</span>
                                @endif
                            </td>

                            <td class="p-5 text-right">
                                {{-- Form Update Status --}}
                                <form action="{{ route('admin.user.status', $user->id) }}" method="POST" class="flex gap-2 justify-end" onsubmit="showLoading()">
                                    @csrf @method('PUT')
                                    <select name="status" class="bg-[#05070a] text-white border ds-border rounded-lg px-3 py-2 text-xs w-32 font-semibold cursor-pointer outline-none focus:border-[#20d981]">
                                        <option value="pending"  {{ $user->status == 'pending'  ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $user->status == 'approved' ? 'selected' : '' }}>Approve</option>
                                        <option value="rejected" {{ $user->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                                        <option value="failed"   {{ $user->status == 'failed'   ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    <button type="submit" class="bg-[#1e293b] hover:bg-[#334155] border border-gray-700 text-white text-xs font-extrabold px-4 py-2 rounded-lg transition">Update</button>
                                </form>

                                {{-- TOMBOL HAPUS & POPUP RESET (HANYA MUNCUL JIKA SUPERADMIN) --}}
                                @if(auth()->user()->isSuperAdmin())
                                    <div class="flex gap-2 justify-end mt-2">
                                        {{-- TOMBOL TRIGGER POPUP RESET PASSWORD --}}
                                        <button type="button" onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Reset Password" class="bg-yellow-500/10 text-yellow-500 hover:bg-yellow-500 hover:text-black border border-yellow-500/30 px-3 py-1.5 rounded-lg text-[10px] font-extrabold uppercase tracking-widest transition">
                                            🔑 Reset Pass
                                        </button>
                                        
                                        <form action="{{ route('superadmin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Anda yakin ingin MENGHAPUS PERMANEN akun ini beserta datanya?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus Permanen" class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white border border-red-500/30 px-3 py-1.5 rounded-lg text-[10px] font-extrabold uppercase tracking-widest transition">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center text-gray-600 font-extrabold">
                                <div class="text-3xl mb-3">👻</div>
                                Belum ada user yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Bukti Pembayaran --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black/95 z-50 flex items-center justify-center p-6 backdrop-blur-xl">
        <div class="relative max-w-3xl w-full">
            <button onclick="closeModal()" class="absolute -top-12 right-0 text-gray-400 hover:text-white text-4xl font-black">&times;</button>
            <img id="modalImage" src="" alt="Bukti" class="w-full h-auto rounded-2xl border border-gray-800 object-contain max-h-[85vh]">
        </div>
    </div>

    {{-- MODAL POP-UP RESET PASSWORD MANUAL --}}
    <div id="resetPasswordModal" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="relative max-w-md w-full bg-[#0f131a] border ds-border p-6 rounded-2xl shadow-[0_15px_30px_rgba(0,0,0,0.5)]">
            <button type="button" onclick="closeResetModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl font-bold transition">&times;</button>
            <h2 class="text-xl font-extrabold text-white mb-2 flex items-center gap-2">
                <span class="text-yellow-500">🔑</span> Reset Password
            </h2>
            <p class="text-xs text-gray-400 mb-6 border-b ds-border pb-4">Silakan ketik manual password baru untuk <strong id="resetUserName" class="text-white"></strong>.</p>

            <form id="resetPasswordForm" method="POST" onsubmit="showLoading()">
                @csrf @method('PUT')
                <div class="mb-6">
                    <label class="text-[10px] text-gray-500 font-extrabold uppercase tracking-widest mb-2 block">Password Baru <span class="text-red-500">*</span></label>
                    <input type="text" name="new_password" required minlength="6" placeholder="Ketik password baru disini..." autocomplete="off"
                        class="w-full bg-[#05070a] text-white border ds-border rounded-xl p-3 outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition text-sm font-medium">
                    <span class="text-[10px] text-gray-500 mt-1 block italic">*Minimal 6 karakter.</span>
                </div>

                <div class="flex gap-3 justify-end mt-4">
                    <button type="button" onclick="closeResetModal()" class="bg-[#1e293b] hover:bg-[#334155] border border-gray-700 text-white font-bold py-2.5 px-5 rounded-xl transition text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-black font-extrabold py-2.5 px-5 rounded-xl transition shadow-[0_0_15px_rgba(234,179,8,0.3)] text-xs uppercase tracking-widest">
                        Konfirmasi Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Loading Screen
        window.addEventListener('load', function () {
            const loader = document.getElementById('loading-screen');
            loader.classList.add('fade-out');
            setTimeout(() => { loader.style.display = 'none'; }, 400);
        });
        function showLoading() {
            const loader = document.getElementById('loading-screen');
            loader.style.display = 'flex';
            setTimeout(() => { loader.classList.remove('fade-out'); }, 10);
        }

        // Modal Bukti Pembayaran
        function openModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
            setTimeout(() => { document.getElementById('modalImage').src = ''; }, 300);
        }
        document.getElementById('imageModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        // ============================================
        // LOGIKA JAVASCRIPT POP-UP RESET PASSWORD 
        // ============================================
        function openResetModal(userId, userName) {
            // Tulis nama user di dalam modal
            document.getElementById('resetUserName').innerText = userName;
            // Ganti URL tujuan form secara dinamis
            document.getElementById('resetPasswordForm').action = `/superadmin/users/${userId}/reset-password`;
            // Tampilkan pop-up
            document.getElementById('resetPasswordModal').classList.remove('hidden');
        }

        function closeResetModal() {
            document.getElementById('resetPasswordModal').classList.add('hidden');
            // Kosongkan form kalau dibatalkan
            document.getElementById('resetPasswordForm').reset();
        }

        // Tutup jika klik area hitam di luar modal
        document.getElementById('resetPasswordModal').addEventListener('click', function (e) {
            if (e.target === this) closeResetModal();
        });
    </script>
</body>
</html>