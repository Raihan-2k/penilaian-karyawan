<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Kita akan memvalidasi 'nip' sebagai kredensial utama.
     */
    public function rules(): array
    {
        return [
            // Ubah 'email' menjadi 'nip'
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Ini adalah tempat logika otentikasi sebenarnya terjadi.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Coba otentikasi menggunakan NIP sebagai 'username'
        // Laravel akan mencari 'nip' di kolom yang didefinisikan sebagai username otentikasi
        // jika tidak, ia akan mencari 'email' (default)
        if (! Auth::attempt($this->only('nip', 'password'), $this->boolean('remember'))) { // Ubah 'email' menjadi 'nip'
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nip' => trans('auth.failed'), // Ubah pesan error untuk NIP
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     * Pastikan ini tidak membatasi terlalu banyak percobaan login.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [ // Ubah pesan error untuk NIP
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // Ubah kunci throttle dari 'email' menjadi 'nip'
        return Str::transliterate(Str::lower($this->input('nip')).'|'.$this->ip());
    }
}