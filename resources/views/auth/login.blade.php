<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blockped Terminal</title>
    {{-- Favicon sudah disesuaikan dengan tahap 2 --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #05070a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Efek Cahaya Belakang (Glow) */
        .bg-glow {
            position: absolute; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,217,129,0.15) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }
        .bg-glow-blue {
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, rgba(0,0,0,0) 70%);
        }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center overflow-hidden selection:bg-[#20d981] selection:text-black">

    <div class="bg-glow top-[-10%] left-[-10%]"></div>
    <div class="bg-glow bg-glow-blue bottom-[-10%] right-[-10%]"></div>

    <div class="w-full max-w-md bg-[#0f131a]/80 backdrop-blur-xl border border-gray-800/60 p-10 rounded-3xl shadow-[0_20px_40px_rgba(0,0,0,0.4)] relative z-10 transform transition-all hover:border-gray-700/80">
        
        {{-- UPDATE: Header Logo dengan logo-system.png dan warna emas --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold tracking-tight flex items-center justify-center gap-3 mb-2">
                <span class="w-10 h-10 rounded-full bg-yellow-500/20 border border-yellow-500/50 flex items-center justify-center text-yellow-500 font-black text-sm overflow-hidden shadow-[0_0_15px_rgba(234,179,8,0.2)]">
                    <img src="{{ asset('logo-system.png') }}" 
                         class="w-full h-full object-cover rounded-full" 
                         alt="B"
                         onerror="this.style.display='none'; this.parentNode.innerText='B'">
                </span>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">BLOCKPED</span>
            </h1>
            <p class="text-gray-500 font-medium text-sm">Akses Private Dashboard Terminal</p>
        </div>

        @if (session('success'))
            <div class="bg-[#20d981]/10 border border-[#20d981]/30 text-[#20d981] p-4 rounded-xl text-sm mb-6 text-center font-bold backdrop-blur-sm">
                ✨ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm mb-6 text-center font-bold backdrop-blur-sm">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-5">
            @csrf
            <div class="group">
                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1.5 block group-focus-within:text-[#20d981] transition-colors">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" 
                    class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl p-3.5 outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
            </div>
            
            <div class="group">
                <label class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1.5 block group-focus-within:text-[#20d981] transition-colors">Password</label>
                <input type="password" name="password" required placeholder="••••••••" 
                    class="w-full bg-[#05070a] text-white border border-gray-800 rounded-xl p-3.5 outline-none focus:ring-2 focus:ring-[#20d981]/50 focus:border-[#20d981] transition-all placeholder-gray-700">
            </div>

            <div class="flex items-center gap-3 mt-2">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-800 bg-[#05070a] accent-[#20d981] cursor-pointer">
                <label for="remember" class="text-sm text-gray-400 cursor-pointer select-none hover:text-white transition-colors font-medium">Ingat Sesi Saya</label>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-[#20d981] to-[#16a360] hover:from-[#1bb86d] hover:to-[#128a50] text-black font-extrabold py-4 rounded-xl mt-4 transition-all duration-300 shadow-[0_0_20px_rgba(32,217,129,0.2)] hover:shadow-[0_0_30px_rgba(32,217,129,0.4)] hover:-translate-y-0.5 uppercase tracking-widest text-sm">
                Masuk Terminal
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 font-medium">
                Belum memiliki akses? <a href="{{ route('register') }}" class="text-[#20d981] font-bold hover:text-white hover:underline transition-colors drop-shadow-[0_0_10px_rgba(32,217,129,0.3)]">Daftar sekarang</a>
            </p>
        </div>
    </div>

</body>
</html>