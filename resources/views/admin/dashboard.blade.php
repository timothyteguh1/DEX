<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blokpedia</title>
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

    <div class="flex justify-between items-center border-b ds-border pb-6 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-3">
                <span class="text-[#20d981] drop-shadow-[0_0_10px_rgba(32,217,129,0.3)]">👑</span> Panel Admin
            </h1>
            <p class="text-gray-500 font-medium text-sm mt-1">Kelola pendaftaran dan status member Blokpedia Terminal.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST" onsubmit="showLoading()">
            @csrf
            <button type="submit" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-white border border-red-500/30 py-2.5 px-6 rounded-xl font-bold transition shadow-lg">
                Logout Admin
            </button>
        </form>
    </div>

    @if (session('success'))
        <div id="alert-success" class="bg-[#20d981]/10 border border-[#20d981]/30 text-[#20d981] p-4 rounded-2xl font-bold mb-6 flex justify-between items-center backdrop-blur-sm">
            <span>✨ {{ session('success') }}</span>
            <button onclick="document.getElementById('alert-success').remove()" class="text-[#20d981] hover:text-white">✖</button>
        </div>
    @endif

    <div class="ds-bg-panel ds-border border rounded-3xl p-8 mb-8 shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
        <h2 class="text-xl font-extrabold text-white mb-5 flex items-center gap-2">
            <span class="text-blue-400">⚙️</span> Pengaturan Teks Pendaftaran
        </h2>
        <form action="{{ route('admin.settings.update') }}" method="POST" onsubmit="showLoading()">
            @csrf
            <div class="group">
                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2 block group-focus-within:text-blue-400 transition-colors">
                    Informasi Biaya / Rekening / Promo
                </label>
                <textarea name="register_info" rows="3" required class="w-full bg-[#05070a] text-white border ds-border rounded-xl p-4 outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all mb-4 leading-relaxed">{{ $registerInfo }}</textarea>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-extrabold py-3 px-8 rounded-xl transition shadow-[0_0_15px_rgba(37,99,235,0.2)] hover:-translate-y-0.5 text-sm uppercase tracking-widest">
                Simpan Pengaturan
            </button>
        </form>
    </div>

    <div class="ds-bg-panel ds-border border rounded-3xl overflow-hidden shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-[#05070a] text-gray-500 border-b ds-border">
                    <tr>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Nama & Kontak</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Tanggal Daftar</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px] text-center">Bukti Transfer</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px] text-center">Status</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px] text-right">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse ($users as $user)
                        <tr class="hover:bg-[#1e293b]/30 transition-colors">
                            <td class="p-5">
                                <div class="font-extrabold text-white text-base mb-0.5">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 font-medium">📧 {{ $user->email }}</div>
                                <div class="text-xs text-gray-500 font-medium">📱 {{ $user->no_hp }}</div>
                            </td>
                            <td class="p-5 text-gray-400 font-semibold text-xs">
                                {{ $user->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="p-5 text-center">
                                @if($user->payment_proof)
                                    <button onclick="openModal('{{ asset('storage/' . $user->payment_proof) }}')" class="inline-flex items-center gap-2 bg-blue-500/10 text-blue-400 hover:text-white hover:bg-blue-500/20 border border-blue-500/30 px-3.5 py-2 rounded-lg transition text-xs font-bold cursor-pointer">
                                        🖼️ Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-red-500/70 text-xs italic font-medium">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="p-5 text-center">
                                @if($user->status === 'approved')
                                    <span class="bg-[#20d981]/10 text-[#20d981] border border-[#20d981]/30 px-3 py-1.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest shadow-sm">APPROVED</span>
                                @elseif($user->status === 'rejected')
                                    <span class="bg-red-500/10 text-red-400 border border-red-500/30 px-3 py-1.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest shadow-sm">REJECTED</span>
                                @else
                                    <span class="bg-yellow-500/10 text-yellow-400 border border-yellow-500/30 px-3 py-1.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest shadow-sm">PENDING</span>
                                @endif
                            </td>
                            <td class="p-5 text-right">
                                <form action="{{ route('admin.user.status', $user->id) }}" method="POST" class="flex gap-2 justify-end" onsubmit="showLoading()">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="bg-[#05070a] text-white border ds-border rounded-lg px-3 py-2 outline-none text-xs w-32 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 font-semibold cursor-pointer">
                                        <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $user->status == 'approved' ? 'selected' : '' }}>Approve (ACC)</option>
                                        <option value="rejected" {{ $user->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                                    </select>
                                    <button type="submit" class="bg-[#1e293b] hover:bg-[#334155] border border-gray-700 text-white text-xs font-extrabold px-4 py-2 rounded-lg transition shadow-md">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-gray-600 font-extrabold">
                                <div class="text-3xl mb-3">👻</div>
                                Belum ada user yang mendaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="imageModal" class="hidden fixed inset-0 bg-black/95 z-50 flex items-center justify-center p-6 backdrop-blur-xl transition-opacity">
        <div class="relative max-w-3xl w-full">
            <button onclick="closeModal()" class="absolute -top-12 right-0 text-gray-400 hover:text-white text-4xl font-black transition">&times;</button>
            <img id="modalImage" src="" alt="Bukti Transfer" class="w-full h-auto rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-gray-800 object-contain max-h-[85vh]">
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('loading-screen');
            loader.classList.add('fade-out');
            setTimeout(() => { loader.style.display = 'none'; }, 400);
        });

        function showLoading() {
            const loader = document.getElementById('loading-screen');
            loader.style.display = 'flex';
            setTimeout(() => { loader.classList.remove('fade-out'); }, 10);
        }

        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
            setTimeout(() => { document.getElementById('modalImage').src = ""; }, 300);
        }

        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) { closeModal(); }
        });
    </script>
</body>
</html>