<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Nome do usuário.',
                'example' => 'Luan',
            ],
            'email' => [
                'description' => 'E-mail do usuário (único).',
                'example' => 'luan@teste.com',
            ],
            'password' => [
                'description' => 'Senha (mínimo 8 caracteres).',
                'example' => 'Senha@1234',
            ],
            'password_confirmation' => [
                'description' => 'Confirmação da senha.',
                'example' => 'Senha@1234',
            ],
            'phone' => [
                'description' => 'Telefone (opcional).',
                'example' => '+55 11 99999-9999',
            ],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->input('name'))]);
        }
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/\s+/', '', $this->input('phone'))]);
        }
    }
}
