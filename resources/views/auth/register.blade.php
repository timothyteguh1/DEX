<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Blokpedia Terminal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #05070a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .bg-glow {
            position: absolute; width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(32,217,129,0.12) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }
        .bg-glow-blue {
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, rgba(0,0,0,0) 70%);
        }
    </style>
</head>

<body class="relative h-screen w-full flex items-center justify-center overflow-hidden selection:bg-[#20d981] selection:text-black">

    <div class="bg-glow top-[-10%] left-[-5%]"></div>
    <div class="bg-glow bg-glow-blue bottom-[-10%] right-[-5%]"></div>

    <div class="w-full max-w-lg bg-[#0f131a]/80 backdrop-blur-xl border border-gray-800/60 p-6 md:p-8 rounded-3xl shadow-[0_20px_40px_rgba(0,0,0,0.5)] relative z-10 scale-[0.95] md:scale-100 origin-center">
        
        <div class="text-center mb-5">
            <h1 class="text-2xl font-extrabold tracking-tight flex items-center justify-center gap-2 mb-1">
                <span class="text-[#20d981] drop-shadow-[0_0_10px_rgba(32,217,129,0.5)]">📊</span> 
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">BLOKPEDIA</span>
            </h1>
            <p class="text-gray-500 font-medium text-xs">Registrasi Akses Private Dashboard</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-xl text-[11px] mb-4 text-center font-bold backdrop-blur-sm">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

       <div class="bg-[#080d17] border border-blue-500/30 rounded-2xl mb-6 relative overflow-hidden shadow-[0_10px_20px_rgba(0,0,0,0.5)]">
            <div class="bg-gradient-to-r from-blue-600/20 to-blue-400/5 px-5 py-3 border-b border-blue-500/30 flex items-center gap-2.5">
                <span class="text-lg drop-shadow-[0_0_8px_rgba(59,130,246,0.8)]">💳</span>
                <h3 class="font-extrabold text-blue-300 text-[11px] uppercase tracking-widest">Instruksi Akses</h3>
            </div>
            <div class="p-5 text-left">
                <p class="text-gray-200 text-xs font-semibold leading-loose tracking-wide">
                    {!! nl2br(e($registerInfo)) !!}
                </p>
            </div>
        </div>
        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3.5">
            @csrf
            
            <div class="group">
                <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: John Doe" 
                    class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl p-5 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" 
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl p-5 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">No WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" required placeholder="081234567890" 
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl p-5 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Password</label>
                    <input type="password" name="password" required placeholder="Minimal 8 char" 
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl p-5 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
                <div class="group">
                    <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1 block group-focus-within:text-[#20d981] transition-colors">Ulangi Pass</label>
                    <input type="password" name="password_confirmation" required placeholder="Ketik ulang" 
                        class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl p-5 text-sm outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
                </div>
            </div>

            <div class="group mt-0.5">
                <label class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1.5 block group-focus-within:text-[#20d981] transition-colors">Upload Bukti / Follow <span class="text-gray-600 normal-case">(Max 2MB)</span></label>
                <input type="file" name="payment_proof" accept="image/*" required 
                    class="block w-full text-[11px] text-gray-400 
                    file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-extrabold file:bg-[#20d981]/10 file:text-[#20d981] hover:file:bg-[#20d981]/20 file:transition-colors file:cursor-pointer
                    border border-gray-800 rounded-xl bg-[#05070a] focus:outline-none focus:ring-2 focus:ring-[#20d981]/50 transition-all cursor-pointer">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-[#20d981] to-[#16a360] hover:from-[#1bb86d] hover:to-[#128a50] text-black font-extrabold py-3 rounded-xl mt-2 transition-all duration-300 shadow-[0_0_15px_rgba(32,217,129,0.2)] hover:shadow-[0_0_25px_rgba(32,217,129,0.4)] hover:-translate-y-0.5 uppercase tracking-widest text-xs">
                Kirim Pendaftaran
            </button>
        </form>

        <div class="mt-5 text-center">
            <p class="text-xs text-gray-500 font-medium">
                Sudah memiliki akun? <a href="{{ route('login') }}" class="text-[#20d981] font-bold hover:text-white hover:underline transition-colors drop-shadow-[0_0_10px_rgba(32,217,129,0.3)]">Masuk di sini</a>
            </p>
        </div>
    </div>

</body>
</html>