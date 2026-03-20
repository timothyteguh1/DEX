<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth; // Tambahan wajib
use App\Models\Coin;

class CoinController extends Controller
{
    public function index()
    {
        // KEAMANAN: Hanya ambil koin milik user yang sedang login!
        $savedCoins = Auth::user()->coins()->orderBy('created_at', 'desc')->get();
        return view('dashboard', compact('savedCoins'));
    }

    public function discover(Request $request)
    {
        // Tangkap input pencarian dari user
        $keyword = $request->input('q');
        
        // Jika user mengetik sesuatu, gunakan itu. 
        if ($keyword) {
            $searchQuery = $keyword;
        } else {
            // JIKA KOSONG: Siapkan daftar kata kunci acak
            $randomKeywords = ['meme', 'doge', 'pepe', 'cat', 'ai', 'inu', 'sol', 'wif', 'moon', 'pump', 'trump', 'elon', 'jup'];
            
            // Pilih satu kata secara acak dari daftar di atas
            $searchQuery = $randomKeywords[array_rand($randomKeywords)];
        }

        // Tembak ke API DexScreener menggunakan kata kunci asli atau acak tadi
        $response = Http::get("https://api.dexscreener.com/latest/dex/search?q={$searchQuery}");
        $pairs = $response->json()['pairs'] ?? [];

        // Kirim data ke tampilan. 
        // Perhatikan: kita tetap mengirim $keyword (bukan $searchQuery) agar kotak pencarian di layar tetap terlihat kosong/bersih.
        return view('discover', compact('pairs', 'keyword'));
    }

    public function store(Request $request)
    {
        // KEAMANAN: Simpan ke brankas user yang login, dan otomatis aktifkan layarnya
        Auth::user()->coins()->updateOrCreate(
            ['token_address' => $request->token_address],
            [
                'name' => $request->name,
                'symbol' => $request->symbol,
                'chain_id' => $request->chain_id,
                'pair_address' => $request->pair_address,
                'is_active' => true, // Otomatis aktif saat baru ditambahkan
            ]
        );

        return redirect('/')->with('success', 'Koin berhasil ditambahkan ke Watchlist!');
    }

    public function destroy(Coin $coin)
    {
        // KEAMANAN: Pastikan user hanya bisa menghapus koinnya sendiri
        if ($coin->user_id === Auth::id()) {
            $coin->delete();
            return redirect('/')->with('success', "Koin {$coin->name} berhasil dihapus dari database!");
        }
        
        return redirect('/')->withErrors('Akses Ditolak.');
    }

    // FUNGSI BARU: Menyimpan memori jika layar koin dibuka/ditutup
    public function toggleActive(Request $request, Coin $coin)
    {
        if ($coin->user_id === Auth::id()) {
            $coin->update(['is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN)]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }
}