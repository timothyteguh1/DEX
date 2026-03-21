<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Siapapun boleh mendaftar
    }

   public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_hp' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
            // Validasi ini memastikan user mengisi angka untuk jawaban captcha
            'captcha_answer' => ['required', 'numeric'],
        ];
    }
}