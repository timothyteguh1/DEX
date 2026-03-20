<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    use HasFactory;

    // Masukkan user_id dan is_active ke fillable
    protected $fillable = ['user_id', 'name', 'symbol', 'token_address', 'chain_id', 'pair_address', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}