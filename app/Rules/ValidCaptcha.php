<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value != session('captcha_result')) {
            $fail('Jawaban verifikasi keamanan salah. Silakan coba lagi.');
        }
    }
}