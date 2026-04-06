@extends('layouts.admin')

@section('title', 'Kelola Admin')
@section('page-title', 'Kelola Admin')
@section('page-desc', 'Tambah, edit, dan hapus akun Admin. Hanya Superadmin.')

@section('content')

    {{-- Form Tambah Admin --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl p-5 mb-6">
        <div class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <span class="text-[#20d981]">➕</span> Tambah Admin Baru
        </div>
        <form action="{{ route('superadmin.admins.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">Nama Lengkap</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nama Admin"
                        class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition placeholder-gray-700">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">Email</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="admin@email.com"
                        class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition placeholder-gray-700">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">No WhatsApp</label>
                    <input type="text" name="no_hp" required value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                        class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition placeholder-gray-700">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1.5 block">Password</label>
                    <input type="text" name="password" required placeholder="Min 6 karakter"
                        class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-[#20d981]/60 transition placeholder-gray-700">
                </div>
            </div>
            <button type="submit" class="bg-[#20d981] hover:bg-[#1bb86d] text-black font-bold py-2.5 px-6 rounded-xl transition text-xs uppercase tracking-widest">
                Buat Admin
            </button>
        </form>
    </div>

    {{-- Daftar Admin --}}
    <div class="bg-[#0c1017] border border-gray-800/40 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Admin</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-center">Role</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest">Dibuat</th>
                        <th class="px-5 py-4 text-[10px] text-gray-500 font-extrabold uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @foreach ($admins as $admin)
                        <tr class="hover:bg-white/[0.015] transition">
                            <td class="px-5 py-4">
                                <div class="font-bold text-white text-[13px]">{{ $admin->name }}</div>
                                <div class="text-[11px] text-gray-500">{{ $admin->email }}</div>
                                <div class="text-[11px] text-gray-600">{{ $admin->no_hp ?? '—' }}</div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($admin->isSuperAdmin())
                                    <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">Super</span>
                                @else
                                    <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">Admin</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs font-semibold">{{ $admin->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                @if($admin->isSuperAdmin())
                                    <span class="text-gray-700 text-[10px] italic">Protected</span>
                                @elseif($admin->id === auth()->id())
                                    <span class="text-gray-700 text-[10px] italic">Akun Anda</span>
                                @else
                                    <div class="flex gap-2 justify-end">
                                        <button onclick="openEditModal({{ $admin->id }}, '{{ addslashes($admin->name) }}', '{{ $admin->email }}', '{{ $admin->no_hp }}')"
                                            class="text-blue-400 hover:text-blue-300 text-[10px] font-bold underline underline-offset-2 transition">Edit</button>
                                        <span class="text-gray-700">·</span>
                                        <form action="{{ route('superadmin.user.destroy', $admin->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus admin {{ addslashes($admin->name) }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-400 text-[10px] font-bold underline underline-offset-2 transition">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Edit Admin --}}
    <div id="editModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm" onclick="if(event.target===this)closeEditModal()">
        <div class="relative max-w-md w-full bg-[#0c1017] border border-gray-800/60 p-6 rounded-2xl">
            <button onclick="closeEditModal()" class="absolute top-4 right-4 text-gray-500 hover:text-white text-xl">&times;</button>
            <h2 class="font-extrabold text-white text-base mb-5">✏️ Edit Admin</h2>

            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="space-y-3 mb-5">
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1 block">Nama</label>
                        <input type="text" name="name" id="edit_name" required
                            class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-blue-500/60 transition">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1 block">Email</label>
                        <input type="email" name="email" id="edit_email" required
                            class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-blue-500/60 transition">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1 block">No WhatsApp</label>
                        <input type="text" name="no_hp" id="edit_no_hp" required
                            class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-blue-500/60 transition">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1 block">Password Baru <span class="text-gray-700 normal-case">(kosongkan jika tidak diubah)</span></label>
                        <input type="text" name="password" placeholder="Opsional"
                            class="w-full bg-[#05070a] text-white text-sm border border-gray-800/60 rounded-xl p-3 outline-none focus:border-blue-500/60 transition placeholder-gray-700">
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeEditModal()" class="bg-[#1e293b] text-white font-bold py-2.5 px-4 rounded-xl text-xs transition hover:bg-[#334155]">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openEditModal(id, name, email, no_hp) {
        document.getElementById('editForm').action = `/superadmin/admins/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_no_hp').value = no_hp;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush