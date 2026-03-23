<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log History - Superadmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #05070a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .ds-bg-panel { background-color: #0f131a; }
        .ds-border { border-color: rgba(31, 41, 55, 0.6); }
    </style>
</head>
<body class="p-4 md:p-8 font-sans relative min-h-screen selection:bg-[#20d981] selection:text-black">

    {{-- Header --}}
    <div class="flex justify-between items-center border-b ds-border pb-6 mb-8">
        <div>
            <div class="font-extrabold text-white text-2xl tracking-wide flex items-center gap-3">
                <span>📜 Log History (Audit Trail)</span>
            </div>
            <p class="text-gray-500 font-medium text-sm mt-1">Sistem pencatatan otomatis setiap aktivitas Admin dan Superadmin.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="bg-[#1e293b] hover:bg-[#334155] border border-gray-700 text-white py-2.5 px-6 rounded-xl font-bold transition">
            ⬅ Kembali ke Dashboard
        </a>
    </div>

    {{-- Tabel Logs --}}
    <div class="ds-bg-panel ds-border border rounded-3xl overflow-hidden shadow-[0_15px_30px_rgba(0,0,0,0.3)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-[#05070a] text-gray-500 border-b ds-border">
                    <tr>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Waktu</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Pelaku (Admin)</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Kategori Aksi</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px]">Deskripsi Detail</th>
                        <th class="p-5 font-extrabold uppercase tracking-widest text-[10px] text-right">IP & Device</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-[#1e293b]/30 transition-colors">
                            <td class="p-5">
                                <div class="font-bold text-white">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }} WIB</div>
                            </td>
                            <td class="p-5">
                                @if($log->user)
                                    <div class="font-bold text-blue-400">{{ $log->user->name }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $log->user->role }}</div>
                                @else
                                    <span class="text-red-500 italic text-xs">Sistem / Akun Terhapus</span>
                                @endif
                            </td>
                            <td class="p-5">
                                <span class="bg-gray-800/50 text-gray-300 border border-gray-700 px-2 py-1 rounded text-[10px] font-extrabold uppercase tracking-widest">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-5 text-gray-300 whitespace-normal min-w-[300px]">
                                {{ $log->description }}
                            </td>
                            <td class="p-5 text-right">
                                <div class="font-mono text-xs text-gray-400">{{ $log->ip_address ?? 'Unknown IP' }}</div>
                                <div class="text-[9px] text-gray-600 mt-1 max-w-[200px] truncate ml-auto" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-16 text-center text-gray-600 font-extrabold">
                                <div class="text-3xl mb-3">📭</div>
                                Belum ada log aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>