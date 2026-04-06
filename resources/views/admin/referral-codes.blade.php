@extends('layouts.admin')

@section('title', 'Kode Referral')
@section('page-title', 'Kode Referral')
@section('page-desc', 'Buat dan kelola kode referral untuk dibagikan ke calon member.')

@section('content')

    {{-- Form Buat Kode --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl p-5 mb-6">
        <div class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <span class="text-[#20d981]">➕</span> Buat Kode Baru
        </div>
        <form action="{{ route('admin.referral-codes.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
            @csrf
            <div class="flex-1 w-full">
                <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">Kode</label>
                <input type="text" name="code" required placeholder="Contoh: BLOCKPED2026" maxlength="50" value="{{ old('code') }}"
                    class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition uppercase font-mono font-bold tracking-wider placeholder-gray-700">
            </div>
            <div class="flex-1 w-full">
                <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">Deskripsi <span class="text-gray-700 normal-case">(opsional)</span></label>
                <input type="text" name="description" placeholder="Contoh: Promo Juni" maxlength="255" value="{{ old('description') }}"
                    class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition placeholder-gray-700">
            </div>
            <button type="submit" class="bg-[#20d981] hover:bg-[#1bb86d] text-black font-bold py-3 px-6 rounded-xl transition text-xs uppercase tracking-wider whitespace-nowrap shrink-0">
                Buat Kode
            </button>
        </form>
    </div>

    {{-- Tabel Kode --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Kode</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Deskripsi</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Pembuat</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Pengguna</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Status</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse ($codes as $code)
                        <tr class="hover:bg-white/[0.015] transition">
                            <td class="px-5 py-4">
                                <span class="font-mono font-extrabold text-white text-[13px] bg-[#05070a] px-3 py-1.5 rounded-lg border border-gray-800/50 select-all">{{ $code->code }}</span>
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs">{{ $code->description ?? '—' }}</td>
                            <td class="px-5 py-4 text-gray-400 text-xs font-semibold">{{ $code->creator->name ?? 'Sistem' }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2.5 py-1 rounded-full text-[10px] font-bold">
                                    {{ $code->users_count }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($code->is_active)
                                    <span class="inline-block w-2 h-2 rounded-full bg-[#20d981] mr-1"></span>
                                    <span class="text-[#20d981] text-[10px] font-bold">Aktif</span>
                                @else
                                    <span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-1"></span>
                                    <span class="text-red-400 text-[10px] font-bold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <form action="{{ route('admin.referral-codes.toggle', $code->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="text-[10px] font-bold px-3 py-1.5 rounded-lg border transition
                                            {{ $code->is_active
                                                ? 'text-yellow-500 border-yellow-500/20 hover:bg-yellow-500/10'
                                                : 'text-emerald-500 border-emerald-500/20 hover:bg-emerald-500/10' }}">
                                            {{ $code->is_active ? '⏸ Off' : '▶ On' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.referral-codes.destroy', $code->id) }}" method="POST" onsubmit="return confirm('Hapus kode {{ $code->code }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 border border-red-500/20 hover:bg-red-500/10 text-[10px] font-bold px-3 py-1.5 rounded-lg transition">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-20 text-center">
                                <div class="text-3xl mb-2">🎟️</div>
                                <div class="text-gray-500 font-bold text-sm">Belum ada kode referral.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection