<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();           // Contoh: Jupiter
            $table->string('symbol')->nullable();         // Contoh: JUP
            $table->string('token_address')->unique();    // Alamat smart contract koin
            $table->string('chain_id');                   // Contoh: solana, bsc, ethereum
            $table->string('pair_address');               // Alamat Liquidity Pool (wajib untuk grafik)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coins');
    }
};