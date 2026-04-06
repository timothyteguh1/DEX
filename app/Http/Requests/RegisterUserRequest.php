<?php

namespace App\Http\Requests;

use App\Rules\IndonesianPhone;
use App\Rules\ValidCaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_hp'          => ['required', 'string', 'max:15', new IndonesianPhone],
            'password'       => ['required', 'confirmed', Password::defaults()],

            'payment_proof'  => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ],

            // Referral code opsional, tapi kalau diisi harus valid & aktif
            'referral_code'  => ['nullable', 'string', 'max:50', 'exists:referral_codes,code'],

            'captcha_answer' => ['required', 'numeric', new ValidCaptcha],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_proof.mimes'    => 'File bukti harus berformat JPG, PNG, atau WEBP.',
            'payment_proof.max'      => 'Ukuran file maksimal 5MB.',
            'referral_code.exists'   => 'Kode referral tidak valid atau tidak ditemukan.',
        ];
    }

    public function attributes(): array
    {
        return [
            'captcha_answer' => 'verifikasi keamanan',
            'no_hp'          => 'nomor WhatsApp',
            'payment_proof'  => 'bukti transfer',
            'referral_code'  => 'kode referral',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Auto uppercase referral code
        if ($this->referral_code) {
            $this->merge([
                'referral_code' => strtoupper(trim($this->referral_code)),
            ]);
        }
    }
}