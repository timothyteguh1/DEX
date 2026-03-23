<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Blockped</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #05070a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-glow { position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(32,217,129,0.12) 0%, rgba(0,0,0,0) 70%); border-radius: 50%; z-index: 0; pointer-events: none; }
        .bg-glow-blue { background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, rgba(0,0,0,0) 70%); }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center overflow-hidden selection:bg-[#20d981] selection:text-black">

    <div class="bg-glow top-[-10%] left-[-10%]"></div>
    <div class="bg-glow bg-glow-blue bottom-[-10%] right-[-10%]"></div>

    <div class="w-full max-w-md bg-[#0f131a]/80 backdrop-blur-xl border border-gray-800/60 p-10 rounded-3xl shadow-[0_20px_40px_rgba(0,0,0,0.4)] relative z-10 text-center mx-4">

        {{-- Logo --}}
        <div class="flex items-center justify-center gap-2 mb-6">
            <span class="w-10 h-10 rounded-full bg-[#20d981]/20 border border-[#20d981]/50 flex items-center justify-center text-[#20d981] font-black overflow-hidden">
                <img src="{{ asset('logo-blokpedia.png') }}" class="w-full h-full object-cover rounded-full" alt="B"
                    onerror="this.style.display='none'; this.parentNode.innerText='B'">
            </span>
            <span class="font-extrabold text-white text-xl tracking-wide">BLOCKPED</span>
        </div>

        <div class="text-5xl mb-4">📬</div>
        <h1 class="text-xl font-extrabold text-white mb-2">Cek Email Anda!</h1>
        <p class="text-gray-400 text-sm leading-relaxed mb-5">
            Link verifikasi telah dikirim ke email Anda.<br>
            Klik link tersebut untuk mengaktifkan akun.
        </p>

        @if (session('success'))
            <div class="bg-[#20d981]/10 border border-[#20d981]/30 text-[#20d981] p-3 rounded-xl text-sm mb-4 font-bold">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-xs mb-5 text-left">
            <p class="font-bold text-red-400 mb-2">⚠️ Penting:</p>
            <ul class="space-y-1.5 text-gray-300">
                <li>📁 Cek folder <strong>Spam / Promosi</strong> jika tidak ada di inbox</li>
                <li>⏳ Akun tidak diverifikasi dalam <strong class="text-yellow-400">1 jam</strong> otomatis <strong class="text-red-400">FAILED</strong></li>
                <li>✉️ Pastikan email yang didaftarkan benar dan aktif</li>
            </ul>
        </div>

        <form action="{{ route('verification.send') }}" method="POST" class="mb-4">
            @csrf
            <button type="submit"
                class="w-full bg-gradient-to-r from-[#20d981] to-[#16a360] hover:from-[#1bb86d] hover:to-[#128a50] text-black font-extrabold py-3.5 rounded-xl transition-all text-sm uppercase tracking-widest">
                🔄 Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs text-gray-500 hover:text-white transition-colors">
                ← Kembali ke Login
            </button>
        </form>
    </div>
</body>
</html>