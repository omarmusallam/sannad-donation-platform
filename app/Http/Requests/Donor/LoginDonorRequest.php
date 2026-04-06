<?php

namespace App\Http\Requests\Donor;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->email)),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'string', 'max:255', 'email:rfc'],
            'password' => ['bail', 'required', 'string', 'max:128'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'email' => (string) $this->input('email'),
            'password' => (string) $this->input('password'),
        ];

        if (!Auth::guard('donor')->attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => [$this->isEn()
                    ? 'The provided login credentials are incorrect.'
                    : 'بيانات تسجيل الدخول غير صحيحة.'],
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = (int) ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => [$this->isEn()
                ? "Too many login attempts. Please try again in {$minutes} minute(s)."
                : "محاولات تسجيل الدخول كثيرة جدًا. يرجى المحاولة مرة أخرى بعد {$minutes} دقيقة."],
        ]);
    }

    public function messages(): array
    {
        return [
            'email.required' => $this->isEn() ? 'Email is required.' : 'البريد الإلكتروني مطلوب.',
            'email.string' => $this->isEn() ? 'The email must be plain text.' : 'يجب أن يكون البريد الإلكتروني نصًا عاديًا.',
            'email.max' => $this->isEn() ? 'The email must not exceed 255 characters.' : 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
            'email.email' => $this->isEn() ? 'Please enter a valid email address.' : 'يرجى إدخال بريد إلكتروني صحيح.',
            'password.required' => $this->isEn() ? 'Password is required.' : 'كلمة المرور مطلوبة.',
            'password.string' => $this->isEn() ? 'The password must be plain text.' : 'يجب أن تكون كلمة المرور نصًا عاديًا.',
            'password.max' => $this->isEn() ? 'The password must not exceed 128 characters.' : 'يجب ألا تزيد كلمة المرور عن 128 حرفًا.',
        ];
    }

    public function attributes(): array
    {
        return $this->isEn()
            ? [
                'email' => 'email address',
                'password' => 'password',
            ]
            : [
                'email' => 'البريد الإلكتروني',
                'password' => 'كلمة المرور',
            ];
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) $this->input('email')) . '|' . $this->ip());
    }

    private function isEn(): bool
    {
        return app()->isLocale('en');
    }
}
