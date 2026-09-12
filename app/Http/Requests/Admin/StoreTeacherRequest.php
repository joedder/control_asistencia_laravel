<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Puedes agregar lógica de permisos de Spatie aquí si es necesario,
        // o devolver true si el middleware del controlador ya lo maneja.
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
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'identity_id' => ['nullable', 'string', 'max:10', 'unique:teachers,identity_id'],
            'english_level' => ['required', 'in:A1,A2,B1,B2,C1,C2'],
            'id_user' => ['nullable', 'exists:users,id'],
        ];
    }
}
