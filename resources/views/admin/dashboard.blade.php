@extends('layouts.admin')

@section('title', 'Dashboard Member')
@section('page-title', 'Dashboard Member')
@section('page-desc', 'Kelola pendaftaran dan status member Blockped.')

@section('content')

    {{-- Pengaturan Teks Pendaftaran (Collapsible) --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl mb-6 overflow-hidden">
        <button onclick="document.getElementById('settings-panel').classList.toggle('hidden')" class="w-full flex items-center justify-between p-5 text-left hover:bg-white/[0.02] transition">
            <div class="flex items-center gap-3">
                <span class="text-xl">⚙️</span>
                <div>
                    <div class="font-bold text-white text-sm">Pengaturan Teks Pendaftaran</div>
                    <div class="text-[11px] text-gray-500">Klik untuk buka/tutup</div>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div id="settings-panel" class="hidden border-t border-gray-800/40 p-5">
            <form action="{{ route('admin.settings.update') }}" method="POST" onsubmit="showLoading()">
                @csrf
                <textarea name="register_info" rows="3" required
                    class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-4 outline-none focus:ring-1 focus:ring-blue-500/50 focus:border-blue-500 transition mb-3 leading-relaxed resize-none">{{ $registerInfo }}</textarea>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-xl transition text-xs uppercase tracking-widest">
                    Simpan
                </button>
            </form>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl p-4 mb-6">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, HP, atau referral..."
                class="bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 flex-grow outline-none focus:ring-1 focus:ring-[#20d981]/40 focus:border-[#20d981]/60 transition placeholder-gray-600">

            <select name="status" class="bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition cursor-pointer sm:w-44 font-semibold">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="bg-[#20d981] hover:bg-[#1bb86d] text-black px-5 py-3 rounded-xl font-bold transition text-xs uppercase tracking-wider">Filter</button>
                <a href="{{ route('admin.dashboard') }}" class="bg-[#1e293b] hover:bg-[#334155] text-white border border-gray-700/50 px-5 py-3 rounded-xl font-bold transition text-xs">Reset</a>
            </div>
        </form>
    </div>

    {{-- Tabel Member --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Member</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Verifikasi</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Referral</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Bukti</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Status</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse ($users as $user)
                        <tr class="hover:bg-white/[0.015] transition">
                            {{-- Member Info --}}
                            <td class="px-5 py-4">
                                <div class="font-bold text-white text-[13px]">{{ $user->name }}</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">{{ $user->email }}</div>
                                <div class="text-[11px] text-gray-600">{{ $user->no_hp }} · {{ $user->created_at->format('d M Y') }}</div>
                            </td>

                            {{-- Email Verified --}}
                            <td class="px-5 py-4">
                                @if ($user->email_verified_at)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#20d981]"></span>
                                        <span class="text-[#20d981] text-[11px] font-bold">Verified</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                        <span class="text-red-400 text-[11px] font-bold">Belum</span>
                                    </div>
                                    @php $sisa = 60 - (int)$user->created_at->diffInMinutes(now()); @endphp
                                    <div class="text-[10px] {{ $sisa > 0 ? 'text-yellow-500' : 'text-red-500' }} mt-0.5">
                                        {{ $sisa > 0 ? "⏳ {$sisa}m lagi" : '⛔ Expired' }}
                                    </div>
                                @endif
                            </td>

                            {{-- Referral --}}
                            <td class="px-5 py-4">
                                @if($user->referral_code)
                                    <span class="font-mono font-bold text-[#20d981] bg-[#20d981]/10 px-2.5 py-1 rounded-lg text-[10px] border border-[#20d981]/20">{{ $user->referral_code }}</span>
                                @else
                                    <span class="text-gray-600 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Bukti --}}
                            <td class="px-5 py-4 text-center">
                                @if ($user->payment_proof)
                                    <button onclick="openModal('{{ asset('storage/' . $user->payment_proof) }}')"
                                        class="text-blue-400 hover:text-blue-300 text-xs font-bold underline underline-offset-2 transition">
                                        Lihat
                                    </button>
                                @else
                                    <span class="text-gray-700 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-5 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'pending'  => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                                        'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                        'failed'   => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                                    ];
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest border {{ $statusColors[$user->status] ?? '' }}">
                                    {{ $user->status }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('admin.user.status', $user->id) }}" method="POST" class="flex gap-2 justify-end items-center" onsubmit="showLoading()">
                                    @csrf @method('PUT')
                                    <select name="status" class="bg-[#05070a] text-white text-xs border border-gray-800/60 rounded-lg px-2.5 py-2 font-semibold cursor-pointer outline-none focus:border-[#20d981]/60 w-28">
                                        <option value="pending"  {{ $user->status == 'pending'  ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $user->status == 'approved' ? 'selected' : '' }}>Approve</option>
                                        <option value="rejected" {{ $user->status == 'rejected' ? 'selected' : '' }}>Reject</option>
                                        <option value="failed"   {{ $user->status == 'failed'   ? 'selected' : '' }}>Failed</option>
                                    </select>
                                    <button type="submit" class="bg-[#1e293b] hover:bg-[#334155] border border-gray-700/50 text-white text-[10px] font-bold px-3 py-2 rounded-lg transition">Ubah</button>
                                </form>

                                @if(auth()->user()->isSuperAdmin())
                                    <div class="flex gap-1.5 justify-end mt-2">
                                        <button onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="text-yellow-500 hover:text-yellow-400 text-[10px] font-bold underline underline-offset-2 transition">Reset Pass</button>
                                        <span class="text-gray-700">·</span>
                                        <form action="{{ route('superadmin.user.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permanen {{ addslashes($user->name) }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-400 text-[10px] font-bold underline underline-offset-2 transition">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-20 text-center">
                                <div class="text-3xl mb-2">👻</div>
                                <div class="text-gray-500 font-bold text-sm">Tidak ada member ditemukan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Bukti --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-6 backdrop-blur-sm" onclick="if(event.target===this)closeModal()">
        <div class="relative max-w-2xl w-full">
            <button onclick="closeModal()" class="absolute -top-10 right-0 text-gray-400 hover:text-white text-3xl font-bold">&times;</button>
            <img id="modalImage" src="" alt="Bukti" class="w-full h-auto rounded-2xl border border-gray-800 object-contain max-h-[80vh]">
        </div>
    </div>

    {{-- Modal Reset Password --}}
    <div id="resetModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm" onclick="if(event.target===this)closeResetModal()">
        <div class="relative max-w-sm w-full bg-[#0c1017] border border-gray-800/60 p-6 rounded-2xl">
            <button onclick="closeResetModal()" class="absolute top-4 right-4 text-gray-500 hover:text-white text-xl">&times;</button>
            <h2 class="font-extrabold text-white text-base mb-1">🔑 Reset Password</h2>
            <p class="text-xs text-gray-500 mb-5">Password baru untuk <strong id="resetUserName" class="text-white"></strong></p>

            <form id="resetForm" method="POST" onsubmit="showLoading()">
                @csrf @method('PUT')
                <input type="text" name="new_password" required minlength="6" placeholder="Password baru (min 6 karakter)" autocomplete="off"
                    class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-yellow-500/60 transition mb-4">
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeResetModal()" class="bg-[#1e293b] text-white font-bold py-2.5 px-4 rounded-xl text-xs transition hover:bg-[#334155]">Batal</button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-2.5 px-4 rounded-xl text-xs transition">Reset</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }
    function openResetModal(id, name) {
        document.getElementById('resetUserName').innerText = name;
        document.getElementById('resetForm').action = `/superadmin/users/${id}/reset-password`;
        document.getElementById('resetModal').classList.remove('hidden');
    }
    function closeResetModal() {
        document.getElementById('resetModal').classList.add('hidden');
        document.getElementById('resetForm').reset();
    }
</script>
@endpush