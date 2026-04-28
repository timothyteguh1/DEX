<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Blockped Terminal</title>
    
    {{-- UPDATE FAVICON --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #05070a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-glow {
            position: absolute; width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(32, 217, 129, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }
        .bg-glow-blue {
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
        }
    </style>
</head>

<body class="relative min-h-screen w-full flex items-center justify-center py-8 overflow-x-hidden selection:bg-[#20d981] selection:text-black">

    <div class="bg-glow top-[-10%] left-[-5%]"></div>
    <div class="bg-glow bg-glow-blue bottom-[-10%] right-[-5%]"></div>

    <div class="w-full max-w-lg bg-[#0f131a]/80 backdrop-blur-xl border border-gray-800/60 p-6 md:p-8 rounded-3xl shadow-[0_20px_40px_rgba(0,0,0,0.5)] relative z-10 mx-4">

        {{-- UPDATE: Header dengan logo-system.png agar konsisten dengan halaman Login --}}
        <div class="text-center mb-5">
            <h1 class="text-2xl font-extrabold tracking-tight flex items-center justify-center gap-2 mb-1">
                <span class="w-8 h-8 rounded-full bg-yellow-500/20 border border-yellow-500/50 flex items-center justify-center text-yellow-500 font-black text-sm overflow-hidden shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                    <img src="{{ asset('logo-system.png') }}"
                        class="w-full h-full object-cover rounded-full"
                        alt="B"
                        onerror="this.style.display='none'; this.parentNode.innerText='B'">
                </span>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">BLOCKPED</span>
            </h1>
            <p class="text-gray-500 font-medium text-xs">Registrasi Akses Private Dashboard</p>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-xl text-[11px] mb-4 text-center font-bold backdrop-blur-sm">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        {{-- Info Box --}}
        <div class="bg-[#080d17] border border-blue-500/30 rounded-2xl mb-5 relative overflow-hidden shadow-[0_10px_20px_rgba(0,0,0,0.5)]">
            <div class="bg-gradient-to-r from-blue-600/20 to-blue-400/5 px-5 py-3 border-b border-blue-500/30 flex items-center gap-2.5">
                <span class="text-lg">💳</span>
                <h3 class="font-extrabold text-blue-300 text-[11px] uppercase tracking-widest">Instruksi Akses</h3>
            </div>
            <div class="p-4 text-left">
                <p class="text-gray-200 text-xs font-semibold leading-loose tracking-wide">
                    {!! nl2br(e($registerInfo)) !!}
                </p>
            </div>
        </div>

        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
            @csrf

            {{-- Nama --}}
            <div class="group">
                <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: John Doe"
                    class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
            </div>

            {{-- Email & No HP --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">No WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx"
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Password</label>
                    <input type="password" name="password" required placeholder="Minimal 8 char"
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Ulangi Pass</label>
                    <input type="password" name="password_confirmation" required placeholder="Ketik ulang"
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
            </div>

            {{-- Upload & Preview (REVISI) --}}
            <div class="group">
                <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1.5 block group-focus-within:text-[#20d981] transition-colors">
                    Upload Bukti / Follow
                    <span class="text-gray-600 normal-case font-normal">(JPG/PNG/WEBP, maks 5MB)</span>
                </label>
                {{-- Tambah ID "payment_proof" untuk ditangkap Javascript --}}
                <input type="file" name="payment_proof" id="payment_proof" accept=".jpg,.jpeg,.png,.webp" required
                    class="block w-full text-[11px] text-gray-400
                    file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-extrabold file:bg-[#20d981]/10 file:text-[#20d981] hover:file:bg-[#20d981]/20 file:transition-colors file:cursor-pointer
                    border border-gray-800 rounded-xl bg-[#05070a] focus:outline-none focus:ring-2 focus:ring-[#20d981]/50 transition-all cursor-pointer p-2">
                
                {{-- Container Preview Gambar --}}
                <div id="preview-container" class="mt-3 hidden">
                    <img id="image-preview" src="" alt="Preview" class="w-full h-auto max-h-48 object-cover rounded-xl border border-gray-700 shadow-md">
                </div>
            </div>

            {{-- REFERRAL CODE --}}
            <div class="group">
                <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">
                    Kode Referral <span class="text-gray-600 normal-case font-normal">(opsional)</span>
                </label>
                <input type="text" name="referral_code" value="{{ old('referral_code') }}" placeholder="Masukkan kode referral jika ada"
                    class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700 uppercase font-mono tracking-wider">
            </div>

            {{-- Captcha --}}
            <div class="group">
                <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1.5 block">Verifikasi Keamanan</label>
                <div class="flex items-center gap-3">
                    <div class="bg-[#151924] border border-gray-800 rounded-xl flex items-center justify-center gap-2 h-10 px-4 select-none w-1/3">
                        <span class="text-[#20d981] font-black text-lg">{{ $num1 }}</span>
                        <span class="text-white font-bold text-lg">+</span>
                        <span class="text-[#20d981] font-black text-lg">{{ $num2 }}</span>
                        <span class="text-gray-500 font-bold text-lg">=</span>
                    </div>
                    <input type="number" name="captcha_answer" required placeholder="Berapa hasilnya?"
                        class="w-2/3 bg-[#05070a] text-white border border-gray-800 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700 h-10">
                </div>
            </div>

            {{-- Info verifikasi email --}}
            <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-3 text-[10px] text-blue-300 flex items-start gap-2">
                <span class="text-base leading-none mt-0.5 shrink-0">📧</span>
                <span>Setelah mendaftar, <strong>cek email Anda</strong> untuk verifikasi akun. Email tidak diverifikasi dalam <strong class="text-yellow-400">1 jam</strong> akan otomatis <strong class="text-red-400">FAILED</strong>.</span>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-[#20d981] to-[#16a360] hover:from-[#1bb86d] hover:to-[#128a50] text-black font-extrabold py-3 rounded-xl transition-all duration-300 shadow-[0_0_15px_rgba(32,217,129,0.2)] hover:shadow-[0_0_25px_rgba(32,217,129,0.4)] hover:-translate-y-0.5 uppercase tracking-widest text-xs mt-1">
                Kirim Pendaftaran
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500 font-medium">
                Sudah memiliki akun? <a href="{{ route('login') }}"
                    class="text-[#20d981] font-bold hover:text-white hover:underline transition-colors">Masuk di sini</a>
            </p>
        </div>
    </div>

    {{-- REVISI: Script untuk memunculkan preview gambar --}}
    <script>
        document.getElementById('payment_proof').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('preview-container');
            const imagePreview = document.getElementById('image-preview');

            if (file) {
                // Buat URL sementara untuk gambar yang dipilih
                imagePreview.src = URL.createObjectURL(file);
                previewContainer.classList.remove('hidden'); // Tampilkan div preview
            } else {
                imagePreview.src = "";
                previewContainer.classList.add('hidden'); // Sembunyikan jika batal pilih gambar
            }
        });
    </script>
</body>
</html>