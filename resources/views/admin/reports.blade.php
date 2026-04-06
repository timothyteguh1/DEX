@extends('layouts.admin')

@section('title', 'Laporan Member')
@section('page-title', 'Laporan Member')
@section('page-desc', 'Statistik pendaftaran member — dengan dan tanpa kode referral.')

@section('content')

    {{-- Filter --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl p-4 mb-6">
        <form action="{{ route('admin.reports') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div>
                <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">Tahun</label>
                <select name="year" class="bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition cursor-pointer font-semibold">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">Bulan</label>
                <select name="month" class="bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition cursor-pointer font-semibold">
                    <option value="">Semua Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-[#20d981] hover:bg-[#1bb86d] text-black px-5 py-3 rounded-xl font-bold transition text-xs uppercase tracking-wider">Tampilkan</button>
            <a href="{{ route('admin.reports') }}" class="bg-[#1e293b] hover:bg-[#334155] text-white border border-gray-700/50 px-5 py-3 rounded-xl font-bold transition text-xs">Reset</a>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl p-5 text-center">
            <div class="text-3xl font-black text-white">{{ $totalMembers }}</div>
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Total Member</div>
        </div>
        <div class="bg-[#0c1017] border border-[#20d981]/15 rounded-2xl p-5 text-center">
            <div class="text-3xl font-black text-[#20d981]">{{ $withReferral }}</div>
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Dengan Referral</div>
            <div class="text-[10px] text-[#20d981]/50 mt-0.5">{{ $totalMembers > 0 ? round(($withReferral / $totalMembers) * 100, 1) : 0 }}%</div>
        </div>
        <div class="bg-[#0c1017] border border-yellow-500/15 rounded-2xl p-5 text-center">
            <div class="text-3xl font-black text-yellow-400">{{ $withoutReferral }}</div>
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Tanpa Referral</div>
            <div class="text-[10px] text-yellow-400/50 mt-0.5">{{ $totalMembers > 0 ? round(($withoutReferral / $totalMembers) * 100, 1) : 0 }}%</div>
        </div>
    </div>

    {{-- Progress Bar --}}
    @if($totalMembers > 0)
        <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl p-5 mb-6">
            <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-3">Rasio Referral</div>
            <div class="flex h-3 rounded-full overflow-hidden gap-0.5 bg-gray-900">
                @if($withReferral > 0)
                    <div class="bg-[#20d981] rounded-l-full transition-all" style="width: {{ ($withReferral / $totalMembers) * 100 }}%"></div>
                @endif
                @if($withoutReferral > 0)
                    <div class="bg-yellow-500 rounded-r-full transition-all" style="width: {{ ($withoutReferral / $totalMembers) * 100 }}%"></div>
                @endif
            </div>
            <div class="flex justify-between mt-2 text-[10px] font-bold">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#20d981]"></span> <span class="text-gray-400">Referral ({{ $withReferral }})</span></span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> <span class="text-gray-400">Non-referral ({{ $withoutReferral }})</span></span>
            </div>
        </div>
    @endif

    {{-- Breakdown Per Bulan --}}
    @if(!$month && count($monthlyBreakdown) > 0)
        <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-800/40">
                <h2 class="text-sm font-bold text-white">📅 Per Bulan — {{ $year }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-800/40">
                            <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-left">Bulan</th>
                            <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Total</th>
                            <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Referral</th>
                            <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Non-referral</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/30">
                        @foreach ($monthlyBreakdown as $m => $d)
                            @if($d['total'] > 0)
                                <tr class="hover:bg-white/[0.015] transition">
                                    <td class="px-5 py-3 font-semibold text-white text-[13px]">{{ $d['month_name'] }}</td>
                                    <td class="px-5 py-3 text-center font-bold text-white">{{ $d['total'] }}</td>
                                    <td class="px-5 py-3 text-center text-[#20d981] font-bold">{{ $d['with_referral'] }}</td>
                                    <td class="px-5 py-3 text-center text-yellow-400 font-bold">{{ $d['without_referral'] }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Breakdown Per Kode Referral --}}
    @if($referralBreakdown->isNotEmpty())
        <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-800/40">
                <h2 class="text-sm font-bold text-white">🎟️ Per Kode Referral</h2>
            </div>
            <div class="p-5 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($referralBreakdown as $ref)
                    <div class="bg-[#05070a] border border-gray-800/40 rounded-xl p-4 text-center">
                        <div class="font-mono font-bold text-[#20d981] text-xs mb-1">{{ $ref['code'] }}</div>
                        <div class="text-2xl font-black text-white">{{ $ref['count'] }}</div>
                        <div class="text-[9px] text-gray-600 font-semibold uppercase mt-0.5">member</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Detail Member --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800/40 flex items-center justify-between">
            <h2 class="text-sm font-bold text-white">📋 Detail Member ({{ $totalMembers }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800/40">
                        <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-left">#</th>
                        <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-left">Member</th>
                        <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Referral</th>
                        <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Status</th>
                        <th class="px-5 py-3 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-right">Tgl Daftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse ($detailUsers as $i => $user)
                        <tr class="hover:bg-white/[0.015] transition">
                            <td class="px-5 py-3 text-gray-600 font-bold text-xs">{{ $i + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-white text-[13px]">{{ $user->name }}</div>
                                <div class="text-[11px] text-gray-500">{{ $user->email }} · {{ $user->no_hp }}</div>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($user->referral_code)
                                    <span class="font-mono font-bold text-[#20d981] text-[10px]">{{ $user->referral_code }}</span>
                                @else
                                    <span class="text-gray-700">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php
                                    $sc = [
                                        'approved' => 'text-emerald-400',
                                        'pending'  => 'text-yellow-400',
                                        'rejected' => 'text-red-400',
                                        'failed'   => 'text-orange-400',
                                    ];
                                @endphp
                                <span class="text-[10px] font-bold uppercase {{ $sc[$user->status] ?? 'text-gray-500' }}">{{ $user->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right text-gray-400 text-xs font-semibold">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-gray-600 font-bold text-sm">Tidak ada member untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection