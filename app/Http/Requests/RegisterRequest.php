<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "identification_number" => "required|string|max:255",
            "email" => ["required", "email", "string", Rule::unique(User::class, "email")],
            "password" => "required|string|min:8|confirmed"

        ];
    }
    public function messages(): array
    {
        return [
            'identification_number.required' => 'El número de identificación es obligatorio.',
            'identification_number.string' => 'El número de identificación debe ser una cadena de texto.',
            'identification_number.max' => 'El número de identificación no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.'
        ];
    }
}
