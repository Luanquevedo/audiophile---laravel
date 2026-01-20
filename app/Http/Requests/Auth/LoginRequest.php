<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'E-mail do usuário.',
                'example' => 'luan@teste.com',
            ],
            'password' => [
                'description' => 'Senha do usuário.',
                'example' => 'Senha@1234',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
    }
}
