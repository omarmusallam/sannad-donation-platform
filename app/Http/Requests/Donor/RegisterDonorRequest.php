<?php

namespace App\Http\Requests\Donor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => filled($this->name) ? Str::squish($this->name) : null,
            'email' => Str::lower(trim((string) $this->email)),
        ]);
    }

    public function rules(): array
    {
        $isEn = app()->isLocale('en');

        $emailRule = extension_loaded('intl')
            ? 'email:rfc,dns,spoof'
            : 'email:rfc';

        return [
            'name' => [
                'nullable',
                'string',
                'min:2',
                'max:120',
            ],

            'email' => [
                'required',
                'string',
                'max:255',
                $emailRule,
                Rule::unique('donors', 'email'),
            ],

            'password' => [
                'required',
                'string',
                'min:10',
                'max:128',
                'confirmed',
                function ($attribute, $value, $fail) use ($isEn) {
                    $value = (string) $value;

                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail($isEn
                            ? 'The password must contain at least one uppercase letter.'
                            : 'يجب أن تحتوي كلمة المرور على حرف كبير واحد على الأقل.');
                    }

                    if (!preg_match('/[a-z]/', $value)) {
                        $fail($isEn
                            ? 'The password must contain at least one lowercase letter.'
                            : 'يجب أن تحتوي كلمة المرور على حرف صغير واحد على الأقل.');
                    }

                    if (!preg_match('/[0-9]/', $value)) {
                        $fail($isEn
                            ? 'The password must contain at least one number.'
                            : 'يجب أن تحتوي كلمة المرور على رقم واحد على الأقل.');
                    }

                    if (!preg_match('/[\W_]/', $value)) {
                        $fail($isEn
                            ? 'The password must contain at least one symbol.'
                            : 'يجب أن تحتوي كلمة المرور على رمز واحد على الأقل.');
                    }

                    $email = (string) $this->input('email');
                    if ($email !== '' && str_contains(Str::lower($value), Str::before(Str::lower($email), '@'))) {
                        $fail($isEn
                            ? 'The password must not contain the email name.'
                            : 'يجب ألا تحتوي كلمة المرور على اسم البريد الإلكتروني.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        $isEn = app()->isLocale('en');

        return [
            'name.string' => $isEn ? 'The name must be text.' : 'يجب أن يكون الاسم نصًا.',
            'name.min' => $isEn ? 'The name must be at least 2 characters.' : 'يجب أن لا يقل الاسم عن حرفين.',
            'name.max' => $isEn ? 'The name may not be greater than 120 characters.' : 'يجب ألا يزيد الاسم عن 120 حرفًا.',

            'email.required' => $isEn ? 'Email is required.' : 'البريد الإلكتروني مطلوب.',
            'email.string' => $isEn ? 'The email must be text.' : 'يجب أن يكون البريد الإلكتروني نصًا.',
            'email.max' => $isEn ? 'The email may not be greater than 255 characters.' : 'يجب ألا يزيد البريد الإلكتروني عن 255 حرفًا.',
            'email.email' => $isEn ? 'Please enter a valid email address.' : 'يرجى إدخال بريد إلكتروني صحيح.',
            'email.unique' => $isEn ? 'This email is already registered.' : 'هذا البريد الإلكتروني مسجل مسبقًا.',

            'password.required' => $isEn ? 'Password is required.' : 'كلمة المرور مطلوبة.',
            'password.string' => $isEn ? 'The password must be text.' : 'يجب أن تكون كلمة المرور نصًا.',
            'password.min' => $isEn ? 'The password must be at least 10 characters.' : 'يجب ألا تقل كلمة المرور عن 10 أحرف.',
            'password.max' => $isEn ? 'The password may not be greater than 128 characters.' : 'يجب ألا تزيد كلمة المرور عن 128 حرفًا.',
            'password.confirmed' => $isEn ? 'Password confirmation does not match.' : 'تأكيد كلمة المرور غير مطابق.',
        ];
    }

    public function attributes(): array
    {
        return app()->isLocale('en')
            ? [
                'name' => 'name',
                'email' => 'email address',
                'password' => 'password',
                'password_confirmation' => 'password confirmation',
            ]
            : [
                'name' => 'الاسم',
                'email' => 'البريد الإلكتروني',
                'password' => 'كلمة المرور',
                'password_confirmation' => 'تأكيد كلمة المرور',
            ];
    }
}
