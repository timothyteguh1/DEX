<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blockped - Dashboard Trading</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #0b0e14;
            color: #e2e8f0;
        }

        .ds-bg-panel {
            background-color: #151924;
        }

        .ds-border {
            border-color: #1e2433;
        }

        .ds-text-gray {
            color: #8b949e;
        }

        .ds-text-green {
            color: #20d981;
        }

        .ds-bg-green {
            background-color: #20d981;
        }

        .ds-text-red {
            color: #ff4d4d;
        }

        .ds-bg-red {
            background-color: #ff4d4d;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #0b0e14;
        }

        ::-webkit-scrollbar-thumb {
            background: #2d3748;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4a5568;
        }

        /* Animasi kedip halus saat angka berubah */
        @keyframes subtlePulse {
            0% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
                text-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
            }

            100% {
                opacity: 1;
            }
        }

        .live-update {
            animation: subtlePulse 0.5s ease-out;
        }

        /* Tambahan: Animasi Transisi Halus Loading Screen */
        .fade-out {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease-out;
        }
    </style>
</head>

<body class="p-2 md:p-3 font-sans h-screen flex flex-col overflow-hidden relative">

    <div id="loading-screen"
        class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-[#0b0e14] transition-opacity duration-300">
        <svg class="animate-spin h-12 w-12 text-[#20d981] mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-80" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span class="text-[#20d981] font-black tracking-widest uppercase text-sm animate-pulse">Menyiapkan
            Terminal...</span>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center pb-3 mb-3 border-b ds-border shrink-0 gap-4 md:gap-0">
        
        <div class="flex items-center gap-3 w-full md:w-1/3 justify-between md:justify-start">
            <h1 class="text-xl font-black text-white tracking-wide flex items-center gap-2">
                <img src="{{ asset('logo-sidebar.png') }}" class="w-12 h-12 w-auto "  alt="Blockped Terminal">
            </h1>
            <span class="text-[9px] bg-green-900/30 text-[#20d981] border border-green-800 px-2 py-0.5 rounded flex items-center gap-1 uppercase tracking-widest font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-[#20d981] animate-pulse"></span> Live
            </span>
        </div>

        <div class="flex gap-2 items-center w-full md:w-1/3 justify-center flex-wrap md:flex-nowrap">
            <select id="coin-selector" class="p-1.5 px-3 rounded text-sm ds-bg-panel text-white border ds-border focus:border-[#20d981] outline-none font-semibold cursor-pointer w-full md:w-auto">
                <option value="" disabled selected>-- Pilih Koin Watchlist --</option>
                @foreach ($savedCoins as $coin)
                    <option value="{{ $coin->id }}">{{ $coin->name }} ({{ $coin->symbol }})</option>
                @endforeach
            </select>
            <button id="add-btn" class="ds-bg-green hover:bg-[#1bb86d] text-black font-bold px-4 py-1.5 rounded text-sm transition shadow-[0_0_10px_rgba(32,217,129,0.2)] whitespace-nowrap">
                + Buka Koin
            </button>
            <a href="/discover" onclick="showLoading()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-1.5 rounded text-sm font-bold transition whitespace-nowrap">
                Cari Baru
            </a>
        </div>

        <div class="flex gap-3 items-center w-full md:w-1/3 justify-end">
            <div class="hidden md:flex flex-col text-right mr-2 border-r ds-border pr-4">
                <span class="text-xs font-bold text-white leading-tight">{{ Auth::user()->name }}</span>
                <span class="text-[9px] text-green-400 font-bold tracking-widest uppercase">Member Aktif</span>
            </div>
            
            <button onclick="openProfileModal()" title="Edit Profil" class="bg-blue-900/20 hover:bg-blue-900 text-blue-500 hover:text-white border border-blue-900/50 px-3 py-1.5 rounded text-sm font-bold transition">
                ⚙️
            </button>

            <form action="{{ route('logout') }}" method="POST" class="m-0" onsubmit="showLoading()">
                @csrf
                <button type="submit" title="Keluar" class="bg-red-900/20 hover:bg-red-900 text-red-500 hover:text-white border border-red-900/50 px-4 py-1.5 rounded text-sm font-bold transition uppercase tracking-wider">
                    Logout
                </button>
            </form>
        </div>

    </div>
    @if (session('success'))
        <div id="alert-success"
            class="bg-[#20d981]/10 border border-[#20d981] text-[#20d981] p-2 rounded text-xs font-bold mb-3 shrink-0 flex justify-between items-center">
            <span>✅ {{ session('success') }}</span>
            <button onclick="document.getElementById('alert-success').remove()"
                class="text-[#20d981] hover:text-white">✖</button>
        </div>
    @endif
    @if ($errors->any())
        <div id="alert-error"
            class="bg-red-900/30 border border-red-800 text-red-400 p-2 rounded text-xs font-bold mb-3 shrink-0 flex justify-between items-center">
            <span>⚠️ {{ $errors->first() }}</span>
            <button onclick="document.getElementById('alert-error').remove()"
                class="text-red-400 hover:text-white">✖</button>
        </div>
    @endif

    <div class="flex-grow grid grid-cols-1 xl:grid-cols-2 gap-3 overflow-y-auto pb-4" id="charts-container">
    </div>
    
    <div id="profileModal"
        class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="relative max-w-md w-full ds-bg-panel border ds-border p-6 rounded-lg shadow-2xl">
            <button onclick="closeProfileModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl font-bold transition">&times;</button>
            <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="ds-text-green">⚙️</span> Edit Data Profil
            </h2>

            <form action="{{ route('profile.update') }}" method="POST" onsubmit="showLoading()">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1 block">Nama
                        Lengkap</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" required
                        class="w-full bg-[#0b0e14] text-white border ds-border rounded p-2.5 outline-none focus:border-[#20d981] transition">
                </div>

                <div class="mb-4">
                    <label class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1 block">No
                        WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ Auth::user()->no_hp }}" required
                        class="w-full bg-[#0b0e14] text-white border ds-border rounded p-2.5 outline-none focus:border-[#20d981] transition">
                </div>

                <div class="mb-4">
                    <label class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1 block">Password Baru
                        <span class="text-[9px] text-red-400">(Opsional)</span></label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diubah"
                        class="w-full bg-[#0b0e14] text-white border ds-border rounded p-2.5 outline-none focus:border-[#20d981] transition">
                </div>

                <div class="mb-6">
                    <label class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1 block">Ulangi Password
                        Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ketik ulang password baru"
                        class="w-full bg-[#0b0e14] text-white border ds-border rounded p-2.5 outline-none focus:border-[#20d981] transition">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded transition shadow-lg text-sm uppercase tracking-widest">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
    
    <script>
        const savedCoins = @json($savedCoins);
        const csrfToken = '{{ csrf_token() }}';
        let activeCharts = [];
        let fetchIntervals = {};

        // --- INJEKSI LOGIKA TIMEFRAME MULAI DI SINI ---
        let activeTimeframes = {}; 
        let coinDataStore = {}; 

        function changeTimeframe(coinId, tf) {
            activeTimeframes[coinId] = tf;
            if (coinDataStore[coinId]) {
                renderInfoPanel(coinId, coinDataStore[coinId]);
            }
        }
        // --- INJEKSI LOGIKA TIMEFRAME SELESAI ---

        function showLoading() {
            const loader = document.getElementById('loading-screen');
            loader.style.display = 'flex';
            setTimeout(() => {
                loader.classList.remove('fade-out');
            }, 10);
        }

        function hideLoading() {
            const loader = document.getElementById('loading-screen');
            loader.classList.add('fade-out');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 400);
        }

        // FUNGSI BARU: Simpan status layar ke Database, menggantikan LocalStorage
        function toggleCoinState(coinId, isActive) {
            fetch(`/toggle-active/${coinId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    is_active: isActive
                })
            }).catch(err => console.error("Gagal sinkronisasi layar"));
        }

        function formatCompactNumber(number) {
            if (!number) return '0';
            return new Intl.NumberFormat('en-US', {
                notation: "compact",
                compactDisplay: "short",
                maximumFractionDigits: 2
            }).format(number);
        }

        function timeSince(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + "y ago";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + "mo ago";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + "d ago";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + "h ago";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + "m ago";
            return "Just now";
        }

        function renderChart(coin) {
            if (activeCharts.includes(coin.id)) return;

            activeCharts.push(coin.id);

            // Simpan status layar TERBUKA ke database
            toggleCoinState(coin.id, true);

            // --- SET DEFAULT TIMEFRAME KE 24 JAM SAAT DIBUKA ---
            if (!activeTimeframes[coin.id]) activeTimeframes[coin.id] = 'h24';

            const container = document.getElementById('charts-container');
            const card = document.createElement('div');

            card.className =
                'w-full flex flex-col lg:flex-row border ds-border rounded bg-[#0b0e14] h-[480px] overflow-hidden shadow-lg';
            card.id = `chart-card-${coin.id}`;

            card.innerHTML = `
                <div class="flex-grow h-[300px] lg:h-full relative border-b lg:border-b-0 lg:border-r ds-border">
                    <iframe src="https://dexscreener.com/${coin.chain_id.toLowerCase()}/${coin.pair_address}?embed=1&theme=dark&trades=0&info=0" 
                            class="w-full h-full border-none"></iframe>
                </div>
                <div class="w-full lg:w-[300px] shrink-0 bg-[#0b0e14] p-3 flex flex-col justify-between overflow-hidden" id="info-${coin.id}">
                    <div class="flex flex-col items-center justify-center h-full ds-text-gray gap-2">
                        <svg class="animate-spin h-6 w-6 text-[#20d981]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-[10px] font-bold tracking-widest uppercase">Memuat Data...</span>
                    </div>
                </div>
            `;

            container.prepend(card);

            fetchDataForInfoPanel(coin);
            fetchIntervals[coin.id] = setInterval(() => {
                fetchDataForInfoPanel(coin);
            }, 10000);
        }

        // Fungsi Modal Profil
        function openProfileModal() {
            document.getElementById('profileModal').classList.remove('hidden');
        }

        function closeProfileModal() {
            document.getElementById('profileModal').classList.add('hidden');
        }

        document.getElementById('profileModal').addEventListener('click', function(e) {
            if (e.target === this) closeProfileModal();
        });

        function fetchDataForInfoPanel(coin) {
            const pairUrl =
                `https://api.dexscreener.com/latest/dex/pairs/${coin.chain_id.toLowerCase()}/${coin.pair_address}`;
            fetch(pairUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.pairs && Array.isArray(data.pairs) && data.pairs.length > 0 && data.pairs[0].liquidity
                        ?.usd > 0) {
                        coinDataStore[coin.id] = data.pairs[0]; // SIMPAN DATA KE MEMORI UNTUK TIMEFRAME
                        renderInfoPanel(coin.id, data.pairs[0]);
                    } else {
                        fetchFallbackToken(coin);
                    }
                }).catch(() => fetchFallbackToken(coin));
        }

        function fetchFallbackToken(coin) {
            const tokenUrl = `https://api.dexscreener.com/tokens/v1/${coin.chain_id.toLowerCase()}/${coin.token_address}`;
            fetch(tokenUrl)
                .then(res => res.json())
                .then(data => {
                    let pairsArray = Array.isArray(data) ? data : (data.pairs || []);
                    if (pairsArray.length > 0) {
                        let bestPair = pairsArray.sort((a, b) => (b.liquidity?.usd || 0) - (a.liquidity?.usd || 0))[0];
                        coinDataStore[coin.id] = bestPair;  // SIMPAN DATA KE MEMORI UNTUK TIMEFRAME
                        renderInfoPanel(coin.id, bestPair);
                    } else {
                        showError(coin.id, coin, "Tidak ada likuiditas aktif.");
                    }
                }).catch(() => showError(coin.id, coin, "Gagal terhubung ke API."));
        }

        function showError(coinId, coin, message) {
            document.getElementById(`info-${coinId}`).innerHTML = `
                ${renderHeaderUI(coin, null)}
                <div class="bg-red-900/20 border border-red-900/50 ds-text-red p-2 rounded text-center font-bold text-[10px] mt-4">
                    ⚠️ ${message}
                </div>`;
        }

        function renderHeaderUI(coin, pairData) {
            const baseSymbol = pairData ? (pairData.baseToken?.symbol || 'UNKNOWN') : coin.symbol;
            const quoteSymbol = pairData ? (pairData.quoteToken?.symbol || 'SOL') : 'SOL';
            const chain = pairData ? pairData.chainId : coin.chain_id;
            const dex = pairData ? pairData.dexId : '-';
            const imgUrl = pairData ? (pairData.info?.imageUrl || '') : '';

            return `
                <div class="flex justify-between items-start shrink-0 mb-1">
                    <div class="flex items-center gap-2">
                        ${imgUrl ? `<img src="${imgUrl}" class="w-7 h-7 rounded-full border ds-border">` : `<div class="w-7 h-7 rounded-full ds-bg-panel border ds-border flex items-center justify-center text-[10px] font-bold">${baseSymbol.charAt(0)}</div>`}
                        <div class="flex flex-col">
                            <div class="text-[13px] font-bold text-white leading-tight">${baseSymbol} <span class="ds-text-gray font-normal">/ ${quoteSymbol}</span></div>
                            <div class="text-[9px] ds-text-gray capitalize flex gap-1 items-center mt-0.5">
                                <span class="text-blue-400 font-bold">${chain}</span> > <span>${dex}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-1 shrink-0">
                        <button onclick="removeChart(${coin.id})" title="Tutup Layar" class="ds-bg-panel hover:bg-gray-800 ds-text-gray hover:text-white text-[10px] px-2 py-1 rounded border ds-border transition">✖</button>
                        <form action="/remove-coin/${coin.id}" method="POST" class="m-0" onsubmit="showLoading(); return confirm('Hapus permanen koin ini dari database?');">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" title="Hapus dari Database" class="bg-red-900/30 hover:bg-red-900 text-red-500 hover:text-white text-[10px] px-2 py-1 rounded border border-red-900/50 transition">🗑</button>
                        </form>
                    </div>
                </div>
            `;
        }

        function renderInfoPanel(coinId, pairData) {
            const infoDiv = document.getElementById(`info-${coinId}`);

            const coin = savedCoins.find(c => c.id === coinId);
            const priceUsd = parseFloat(pairData.priceUsd) || 0;
            const priceNative = parseFloat(pairData.priceNative) || 0;
            const baseSymbol = pairData.baseToken?.symbol || 'UNKNOWN';
            const quoteSymbol = pairData.quoteToken?.symbol || 'SOL';

            const m5Change = pairData.priceChange?.m5 || 0;
            const h1Change = pairData.priceChange?.h1 || 0;
            const h6Change = pairData.priceChange?.h6 || 0;
            const h24Change = pairData.priceChange?.h24 || 0;

            // --- AMBIL TIMEFRAME YANG AKTIF ---
            const tf = activeTimeframes[coinId] || 'h24';

            // --- UBAH H24 MENJADI DINAMIS BERDASARKAN TIMEFRAME YANG DIPILIH ---
            const txns = pairData.txns?.[tf] || { buys: 0, sells: 0 };
            const vol = pairData.volume?.[tf] || 0;
            
            const liquidity = pairData.liquidity?.usd || 0;
            const fdv = pairData.fdv || 0;
            const marketCap = pairData.marketCap || pairData.fdv || 0;

            const totalTxns = txns.buys + txns.sells;
            const buyRatio = totalTxns > 0 ? (txns.buys / totalTxns) : 0.5;
            const sellRatio = totalTxns > 0 ? (txns.sells / totalTxns) : 0.5;
            const buyVol = vol * buyRatio;
            const sellVol = vol * sellRatio;

            const estBuyers = Math.floor(txns.buys * 0.45);
            const estSellers = Math.floor(txns.sells * 0.45);
            const totalMakers = estBuyers + estSellers;
            const makerBuyRatio = totalMakers > 0 ? (estBuyers / totalMakers) : 0.5;
            const makerSellRatio = totalMakers > 0 ? (estSellers / totalMakers) : 0.5;

            const pairCreatedStr = pairData.pairCreatedAt ? timeSince(new Date(pairData.pairCreatedAt)) : '-';
            const pooledBase = pairData.liquidity?.base || 0;
            const pooledQuote = pairData.liquidity?.quote || 0;
            const halfLiqUsd = liquidity / 2;

            const colorClass = (val) => val >= 0 ? 'ds-text-green' : 'ds-text-red';
            const sign = (val) => val > 0 ? '+' : '';

            // --- CSS KONDISIONAL UNTUK TOMBOL ---
            const getBtnClass = (currentTf) => {
                return tf === currentTf ? 'bg-[#1e2433] border-b-2 border-[#20d981]' : 'hover:bg-[#1e2433] cursor-pointer';
            };

            infoDiv.innerHTML = `
                ${renderHeaderUI(coin, pairData)}

                <div class="grid grid-cols-2 gap-2 text-center shrink-0">
                    <div class="ds-bg-panel border ds-border rounded py-2 live-update">
                        <div class="text-[9px] ds-text-gray uppercase tracking-wider mb-1">Price USD</div>
                        <div class="text-[16px] font-bold text-white leading-none">$${priceUsd < 0.01 ? priceUsd.toFixed(6) : priceUsd.toFixed(4)}</div>
                    </div>
                    <div class="ds-bg-panel border ds-border rounded py-2 flex flex-col items-center justify-center live-update">
                        <div class="text-[9px] ds-text-gray uppercase tracking-wider mb-1">Price Native</div>
                        <div class="text-[12px] font-bold text-white leading-none">${priceNative.toFixed(7)}</div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 shrink-0 text-center live-update">
                    <div class="ds-bg-panel border ds-border rounded py-2">
                        <div class="text-[8px] ds-text-gray uppercase tracking-wider mb-1">Liquidity</div>
                        <div class="text-[12px] font-bold text-white leading-none">$${formatCompactNumber(liquidity)}</div>
                    </div>
                    <div class="ds-bg-panel border ds-border rounded py-2">
                        <div class="text-[8px] ds-text-gray uppercase tracking-wider mb-1">FDV</div>
                        <div class="text-[12px] font-bold text-white leading-none">$${formatCompactNumber(fdv)}</div>
                    </div>
                    <div class="ds-bg-panel border ds-border rounded py-2">
                        <div class="text-[8px] ds-text-gray uppercase tracking-wider mb-1">Mkt Cap</div>
                        <div class="text-[12px] font-bold text-white leading-none">$${formatCompactNumber(marketCap)}</div>
                    </div>
                </div>

                <div class="flex border ds-border rounded overflow-hidden text-center bg-[#151924] shrink-0 live-update select-none">
                    <div onclick="changeTimeframe(${coinId}, 'm5')" class="flex-1 py-1.5 border-r ds-border transition ${getBtnClass('m5')}">
                        <div class="text-[9px] ${tf === 'm5' ? 'text-white' : 'ds-text-gray'} font-bold mb-0.5">5M</div>
                        <div class="text-[12px] font-bold ${colorClass(m5Change)} leading-none">${sign(m5Change)}${m5Change}%</div>
                    </div>
                    <div onclick="changeTimeframe(${coinId}, 'h1')" class="flex-1 py-1.5 border-r ds-border transition ${getBtnClass('h1')}">
                        <div class="text-[9px] ${tf === 'h1' ? 'text-white' : 'ds-text-gray'} font-bold mb-0.5">1H</div>
                        <div class="text-[12px] font-bold ${colorClass(h1Change)} leading-none">${sign(h1Change)}${h1Change}%</div>
                    </div>
                    <div onclick="changeTimeframe(${coinId}, 'h6')" class="flex-1 py-1.5 border-r ds-border transition ${getBtnClass('h6')}">
                        <div class="text-[9px] ${tf === 'h6' ? 'text-white' : 'ds-text-gray'} font-bold mb-0.5">6H</div>
                        <div class="text-[12px] font-bold ${colorClass(h6Change)} leading-none">${sign(h6Change)}${h6Change}%</div>
                    </div>
                    <div onclick="changeTimeframe(${coinId}, 'h24')" class="flex-1 py-1.5 transition ${getBtnClass('h24')}">
                        <div class="text-[9px] ${tf === 'h24' ? 'text-white' : 'ds-text-gray'} font-bold mb-0.5">24H</div>
                        <div class="text-[12px] font-bold ${colorClass(h24Change)} leading-none">${sign(h24Change)}${h24Change}%</div>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 shrink-0 live-update">
                    <div class="flex items-center gap-3 border-b border-[#1e2433] pb-1.5">
                        <div class="w-[35%] text-left">
                            <div class="text-[9px] ds-text-gray uppercase tracking-wide mb-0.5">TXNS (${tf.toUpperCase()})</div>
                            <div class="text-[12px] font-bold text-white leading-none">${totalTxns.toLocaleString()}</div>
                        </div>
                        <div class="w-[65%]">
                            <div class="flex justify-between text-[8px] uppercase ds-text-gray mb-1"><span>Buys</span><span>Sells</span></div>
                            <div class="flex justify-between text-[10px] font-bold text-white mb-1 leading-none">
                                <span>${txns.buys.toLocaleString()}</span><span>${txns.sells.toLocaleString()}</span>
                            </div>
                            <div class="flex h-1 w-full rounded-full overflow-hidden gap-0.5">
                                <div class="ds-bg-green" style="width: ${buyRatio * 100}%"></div><div class="ds-bg-red" style="width: ${sellRatio * 100}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 border-b border-[#1e2433] pb-1.5">
                        <div class="w-[35%] text-left">
                            <div class="text-[9px] ds-text-gray uppercase tracking-wide mb-0.5">VOLUME (${tf.toUpperCase()})</div>
                            <div class="text-[12px] font-bold text-white leading-none">$${formatCompactNumber(vol)}</div>
                        </div>
                        <div class="w-[65%]">
                            <div class="flex justify-between text-[8px] uppercase ds-text-gray mb-1"><span>Buy Vol</span><span>Sell Vol</span></div>
                            <div class="flex justify-between text-[10px] font-bold text-white mb-1 leading-none">
                                <span>$${formatCompactNumber(buyVol)}</span><span>$${formatCompactNumber(sellVol)}</span>
                            </div>
                            <div class="flex h-1 w-full rounded-full overflow-hidden gap-0.5">
                                <div class="ds-bg-green" style="width: ${buyRatio * 100}%"></div><div class="ds-bg-red" style="width: ${sellRatio * 100}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-[35%] text-left">
                            <div class="text-[9px] ds-text-gray uppercase tracking-wide mb-0.5">MAKERS (${tf.toUpperCase()})</div>
                            <div class="text-[12px] font-bold text-white leading-none">${totalMakers.toLocaleString()}</div>
                        </div>
                        <div class="w-[65%]">
                            <div class="flex justify-between text-[8px] uppercase ds-text-gray mb-1"><span>Buyers</span><span>Sellers</span></div>
                            <div class="flex justify-between text-[10px] font-bold text-white mb-1 leading-none">
                                <span>${estBuyers.toLocaleString()}</span><span>${estSellers.toLocaleString()}</span>
                            </div>
                            <div class="flex h-1 w-full rounded-full overflow-hidden gap-0.5">
                                <div class="ds-bg-green" style="width: ${makerBuyRatio * 100}%"></div><div class="ds-bg-red" style="width: ${makerSellRatio * 100}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#151924] p-2.5 rounded border ds-border flex flex-col gap-2 shrink-0 live-update">
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="ds-text-gray">Pair created</span>
                        <span class="text-white font-bold">${pairCreatedStr}</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="ds-text-gray">Pooled ${baseSymbol}</span>
                        <div class="flex gap-2 items-center">
                            <span class="text-white font-bold">${formatCompactNumber(pooledBase)}</span>
                            <span class="ds-text-green text-[9px]">$${formatCompactNumber(halfLiqUsd)}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="ds-text-gray">Pooled ${quoteSymbol}</span>
                        <div class="flex gap-2 items-center">
                            <span class="text-white font-bold">${formatCompactNumber(pooledQuote)}</span>
                            <span class="ds-text-green text-[9px]">$${formatCompactNumber(halfLiqUsd)}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        function removeChart(coinId) {
            if (fetchIntervals[coinId]) {
                clearInterval(fetchIntervals[coinId]);
                delete fetchIntervals[coinId];
            }

            document.getElementById(`chart-card-${coinId}`)?.remove();
            activeCharts = activeCharts.filter(id => id !== coinId);

            // Simpan status layar DITUTUP ke database
            toggleCoinState(coinId, false);
        }

        document.getElementById('add-btn').addEventListener('click', () => {
            const val = document.getElementById('coin-selector').value;
            if (!val) return alert('Pilih koin!');
            renderChart(savedCoins.find(c => c.id == val));
        });

        // Trigger Loading Out saat halaman selesai dimuat & Baca dari Database
        window.addEventListener('load', function() {
            hideLoading();

            // PENGGANTI LOCALSTORAGE: BACA LANGSUNG DARI DATABASE BACKEND
            const activeCoins = savedCoins.filter(c => c.is_active == 1 || c.is_active === true);

            if (activeCoins.length > 0) {
                // Jika ada riwayat layar terbuka di DB, render semua
                activeCoins.forEach(coin => renderChart(coin));
            } else if (savedCoins.length > 0) {
                // Jika belum ada yang aktif, otomatis buka 1 koin pertama
                renderChart(savedCoins[0]);
            }
        });
    </script>
</body>

</html>