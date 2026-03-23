<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IndonesianPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Format: 08xx, +628xx, 628xx — total 10-15 digit
        $pattern = '/^(\+62|62|0)8[1-9][0-9]{6,10}$/';

        if (!preg_match($pattern, $value)) {
            $fail('Format nomor WhatsApp tidak valid. Gunakan format 08xx, +628xx, atau 628xx (10–15 digit).');
        }
    }
}