@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('page-desc', 'Audit trail — pencatatan otomatis setiap aktivitas Admin dan Superadmin.')

@section('content')

    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Waktu</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Pelaku</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Aksi</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Deskripsi</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-right">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-white/[0.015] transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-semibold text-white text-xs">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-600">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-semibold text-blue-400 text-xs">{{ $log->user->name }}</div>
                                    <div class="text-[9px] text-gray-600 uppercase tracking-widest">{{ $log->user->role }}</div>
                                @else
                                    <span class="text-gray-600 text-xs italic">Terhapus</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $actionColors = [
                                        'UPDATE_STATUS' => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                                        'CREATE_ADMIN'  => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                                        'UPDATE_ADMIN'  => 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
                                        'DELETE_ACCOUNT' => 'text-red-400 bg-red-500/10 border-red-500/20',
                                        'RESET_PASSWORD' => 'text-yellow-400 bg-yellow-500/10 border-yellow-500/20',
                                        'CREATE_REFERRAL' => 'text-purple-400 bg-purple-500/10 border-purple-500/20',
                                        'TOGGLE_REFERRAL' => 'text-orange-400 bg-orange-500/10 border-orange-500/20',
                                        'DELETE_REFERRAL' => 'text-pink-400 bg-pink-500/10 border-pink-500/20',
                                    ];
                                    $ac = $actionColors[$log->action] ?? 'text-gray-400 bg-gray-500/10 border-gray-500/20';
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[9px] font-extrabold uppercase tracking-widest border {{ $ac }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-300 text-xs max-w-[350px]">
                                <div class="truncate" title="{{ $log->description }}">{{ $log->description }}</div>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="font-mono text-[10px] text-gray-500">{{ $log->ip_address ?? '—' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-20 text-center">
                                <div class="text-3xl mb-2">📭</div>
                                <div class="text-gray-500 font-bold text-sm">Belum ada log aktivitas.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection