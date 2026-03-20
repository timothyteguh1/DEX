<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blockped - Cari Koin</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-blokpedia.png') }}">
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

        .ds-text-green {
            color: #20d981;
        }

        .ds-bg-green {
            background-color: #20d981;
        }

        /* Animasi Transisi Halus */
        .fade-out {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease-out;
        }
    </style>
</head>

<body class="p-4 md:p-8 font-sans relative min-h-screen">

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
        <span class="text-[#20d981] font-black tracking-widest uppercase text-sm animate-pulse">Memuat Data...</span>
    </div>

    <div class="max-w-7xl mx-auto ds-bg-panel p-6 rounded-xl border ds-border shadow-2xl">

        <div class="flex flex-col md:flex-row justify-between items-center border-b ds-border pb-5 mb-6 gap-4">
            <h1 class="text-3xl font-black text-white tracking-wide flex items-center gap-3">
                <span class="ds-text-green">🔍</span> Explorer Koin
            </h1>
            <a href="/"
                class="bg-gray-800 hover:bg-gray-700 border ds-border text-white py-2 px-5 rounded-lg font-bold transition duration-200 flex items-center gap-2">
                <span>⬅</span> Kembali ke Dashboard
            </a>
        </div>

        <form action="/discover" method="GET" class="flex flex-col md:flex-row gap-3 mb-8" onsubmit="showLoading()">
            <input type="text" name="q" value="{{ $keyword ?? '' }}"
                placeholder="Cari koin (Contoh: pepe, shiba, atau paste Smart Contract)..."
                class="flex-grow p-4 rounded-lg bg-[#0b0e14] text-white border ds-border focus:border-[#20d981] outline-none text-base font-medium transition placeholder-gray-600">
            <button type="submit"
                class="ds-bg-green hover:bg-[#1bb86d] text-black px-8 py-4 rounded-lg font-black text-base transition shadow-[0_0_15px_rgba(32,217,129,0.2)] uppercase tracking-wider">
                Cari Koin
            </button>
        </form>

        <div class="overflow-x-auto rounded-lg border ds-border bg-[#0b0e14]">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-[#1b202e] text-gray-400 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="p-4 border-b ds-border w-16 text-center">No</th>
                        <th class="p-4 border-b ds-border">Aset Kripto</th>
                        <th class="p-4 border-b ds-border text-right">Harga (USD)</th>
                        <th class="p-4 border-b ds-border text-right">24H%</th>
                        <th class="p-4 border-b ds-border text-center">Jaringan</th>
                        <th class="p-4 border-b ds-border text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1e2433]">
                    @if (isset($pairs) && is_array($pairs) && count($pairs) > 0)
                        @foreach ($pairs as $index => $pair)
                            <tr class="hover:bg-[#151924] transition duration-150">
                                <td class="p-4 font-bold text-gray-500 text-center">{{ $index + 1 }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        @if (isset($pair['info']['imageUrl']))
                                            <img src="{{ $pair['info']['imageUrl'] }}"
                                                class="w-10 h-10 rounded-full border ds-border object-cover"
                                                alt="Logo">
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-full ds-bg-panel border ds-border flex items-center justify-center text-sm font-black text-white">
                                                {{ substr($pair['baseToken']['symbol'], 0, 1) }}
                                            </div>
                                        @endif

                                        <div>
                                            <div class="font-bold text-base text-white">{{ $pair['baseToken']['name'] }}
                                            </div>
                                            <div class="text-xs text-gray-400 font-mono mt-0.5">
                                                {{ $pair['baseToken']['symbol'] }}
                                                <span class="text-gray-600">/
                                                    {{ $pair['quoteToken']['symbol'] ?? 'SOL' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono font-bold text-white text-right text-base">
                                    ${{ isset($pair['priceUsd']) ? (floatval($pair['priceUsd']) < 0.01 ? number_format((float) $pair['priceUsd'], 6) : number_format((float) $pair['priceUsd'], 4)) : '0.00' }}
                                </td>

                                @php
                                    $priceChange = $pair['priceChange']['h24'] ?? 0;
                                    $isUp = $priceChange >= 0;
                                @endphp
                                <td
                                    class="p-4 font-bold text-right text-sm {{ $isUp ? 'ds-text-green' : 'text-red-500' }}">
                                    {{ $isUp ? '+' : '' }}{{ number_format($priceChange, 2) }}%
                                </td>

                                <td class="p-4 text-center">
                                    <span
                                        class="bg-blue-900/30 text-blue-400 py-1.5 px-3 rounded text-[10px] font-black uppercase tracking-wider border border-blue-800">
                                        {{ $pair['chainId'] }}
                                    </span>
                                </td>

                                <td class="p-4 text-right">
                                    <form action="/add-coin" method="POST" class="m-0 inline-block"
                                        onsubmit="showLoading()">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $pair['baseToken']['name'] }}">
                                        <input type="hidden" name="symbol"
                                            value="{{ $pair['baseToken']['symbol'] }}">
                                        <input type="hidden" name="token_address"
                                            value="{{ $pair['baseToken']['address'] }}">
                                        <input type="hidden" name="chain_id" value="{{ $pair['chainId'] }}">
                                        <input type="hidden" name="pair_address" value="{{ $pair['pairAddress'] }}">

                                        <button type="submit"
                                            class="bg-[#1e2433] hover:bg-[#2d3748] border ds-border text-white px-4 py-2 rounded font-bold text-xs transition uppercase tracking-wider">
                                            + Tambah Layar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="p-12 text-center text-gray-500 font-bold">
                                <div class="text-4xl mb-3">👻</div>
                                Tidak ada koin ditemukan. Coba masukkan nama token atau Smart Contract yang benar.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // 1. Sembunyikan Loading Screen saat halaman selesai dimuat (Fade Out)
        window.addEventListener('load', function() {
            const loader = document.getElementById('loading-screen');
            loader.classList.add('fade-out');

            // Hapus elemen dari DOM setelah animasi transisi selesai (0.4 detik)
            setTimeout(() => {
                loader.style.display = 'none';
            }, 400);
        });

        // 2. Tampilkan Loading Screen kembali saat tombol Cari / Tambah Layar ditekan
        function showLoading() {
            const loader = document.getElementById('loading-screen');
            loader.style.display = 'flex';

            // Paksa browser membaca display:flex dulu, baru hapus class fade-out
            setTimeout(() => {
                loader.classList.remove('fade-out');
            }, 10);
        }
    </script>
</body>

</html>
