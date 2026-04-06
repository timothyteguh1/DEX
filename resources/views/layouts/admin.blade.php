<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Blockped</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #05070a; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1rem; border-radius: 0.75rem; font-weight: 700; font-size: 0.8rem; transition: all 0.15s; color: #6b7280; }
        .sidebar-link:hover { background-color: rgba(255,255,255,0.04); color: #e2e8f0; }
        .sidebar-link.active { background-color: rgba(32,217,129,0.1); color: #20d981; border: 1px solid rgba(32,217,129,0.2); }
        .sidebar-link.active .sidebar-icon { color: #20d981; }
        .sidebar-icon { font-size: 1.1rem; width: 1.5rem; text-align: center; flex-shrink: 0; }
        .fade-out { opacity: 0; pointer-events: none; transition: opacity 0.3s ease-out; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }

        /* Mobile overlay */
        #sidebar-overlay { display: none; }
        #sidebar-overlay.show { display: block; }

        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%); transition: transform 0.25s ease; }
            #sidebar.open { transform: translateX(0); }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">

    {{-- Loading Screen --}}
    <div id="loading-screen" class="fixed inset-0 z-[60] flex flex-col items-center justify-center bg-[#05070a] transition-opacity duration-300" style="display:none;">
        <svg class="animate-spin h-10 w-10 text-[#20d981] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-[#20d981] font-bold tracking-widest uppercase text-[10px] animate-pulse">Memproses...</span>
    </div>

    {{-- Mobile Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-30 lg:hidden" onclick="toggleSidebar()"></div>

    {{-- ======================== SIDEBAR ======================== --}}
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-[250px] h-full bg-[#0a0d12] border-r border-gray-800/50 flex flex-col lg:translate-x-0">

        {{-- Logo --}}
        <div class="p-5 pb-4 border-b border-gray-800/50">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-[#20d981]/15 border border-[#20d981]/30 flex items-center justify-center overflow-hidden shrink-0">
                    <img src="{{ asset('logo-blokpedia.png') }}" class="w-full h-full object-cover" alt="B"
                        onerror="this.style.display='none'; this.parentNode.innerHTML='<span class=\'text-[#20d981] font-black text-sm\'>B</span>'">
                </span>
                <div>
                    <div class="font-extrabold text-white text-sm tracking-wide">BLOCKPED</div>
                    <div class="text-[9px] text-gray-500 font-semibold uppercase tracking-widest">Admin Panel</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">

            <div class="text-[9px] text-gray-600 font-extrabold uppercase tracking-[0.15em] px-1 mb-2 mt-1">Menu Utama</div>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon">📋</span> Dashboard Member
            </a>

            <a href="{{ route('admin.referral-codes') }}"
               class="sidebar-link {{ request()->routeIs('admin.referral-codes') ? 'active' : '' }}">
                <span class="sidebar-icon">🎟️</span> Kode Referral
            </a>

            <a href="{{ route('admin.reports') }}"
               class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <span class="sidebar-icon">📊</span> Laporan Member
            </a>

            @if(auth()->user()->isSuperAdmin())
                <div class="text-[9px] text-gray-600 font-extrabold uppercase tracking-[0.15em] px-1 mb-2 mt-5">Superadmin</div>

                <a href="{{ route('superadmin.admins') }}"
                   class="sidebar-link {{ request()->routeIs('superadmin.admins') ? 'active' : '' }}">
                    <span class="sidebar-icon">👥</span> Kelola Admin
                </a>

                <a href="{{ route('superadmin.logs') }}"
                   class="sidebar-link {{ request()->routeIs('superadmin.logs') ? 'active' : '' }}">
                    <span class="sidebar-icon">📜</span> Log Aktivitas
                </a>
            @endif

        </nav>

        {{-- User Info + Logout --}}
        <div class="p-4 border-t border-gray-800/50">
            <div class="flex items-center gap-3 mb-3 px-1">
                <div class="w-8 h-8 rounded-lg bg-[#1e293b] border border-gray-700 flex items-center justify-center text-xs font-black text-white shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[9px] font-bold uppercase tracking-widest {{ auth()->user()->isSuperAdmin() ? 'text-purple-400' : 'text-blue-400' }}">
                        {{ auth()->user()->role }}
                    </div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" onsubmit="showLoading()">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 text-xs font-bold text-red-400 hover:text-white hover:bg-red-500/15 border border-red-500/20 rounded-xl py-2.5 transition">
                    <span>⏻</span> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="lg:ml-[250px] min-h-screen flex flex-col">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-20 bg-[#05070a]/80 backdrop-blur-xl border-b border-gray-800/40 px-5 py-4 flex items-center justify-between gap-4">
            {{-- Mobile hamburger --}}
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div>
                <h1 class="text-lg font-extrabold text-white tracking-tight">@yield('page-title', 'Dashboard')</h1>
                <p class="text-[11px] text-gray-500 font-medium hidden sm:block">@yield('page-desc', '')</p>
            </div>

            <div class="flex items-center gap-2">
                @yield('header-actions')
            </div>
        </header>

        {{-- Alerts --}}
        <div class="px-5 pt-4">
            @if (session('success'))
                <div id="alert-s" class="bg-[#20d981]/8 border border-[#20d981]/25 text-[#20d981] p-3.5 rounded-xl font-bold text-sm mb-4 flex justify-between items-center">
                    <span>✅ {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="hover:text-white ml-4 shrink-0">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div id="alert-e" class="bg-red-500/8 border border-red-500/25 text-red-400 p-3.5 rounded-xl font-bold text-sm mb-4 flex justify-between items-center">
                    <span>⛔ {{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="hover:text-white ml-4 shrink-0">✕</button>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-500/8 border border-red-500/25 text-red-400 p-3.5 rounded-xl font-bold text-sm mb-4">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 p-5">
            @yield('content')
        </main>

    </div>

    {{-- ======================== SCRIPTS ======================== --}}
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('show');
        }
        function showLoading() {
            const l = document.getElementById('loading-screen');
            l.style.display = 'flex';
            setTimeout(() => l.classList.remove('fade-out'), 10);
        }
        function hideLoading() {
            const l = document.getElementById('loading-screen');
            l.classList.add('fade-out');
            setTimeout(() => l.style.display = 'none', 300);
        }
    </script>
    @stack('scripts')
</body>
</html>