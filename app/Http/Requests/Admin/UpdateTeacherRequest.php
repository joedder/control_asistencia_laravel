<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
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
        // En una actualización, obtenemos el ID del profesor actual de la ruta
        $teacher = $this->route('teacher');

        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'identity_id' => [
                'nullable', 
                'string', 
                'max:10',
                // Evita el error "ya existe" si el profesor mantiene su misma cédula
                Rule::unique('teachers', 'identity_id')->ignore($teacher->id ?? null),
            ],
            'english_level' => ['required', 'in:A1,A2,B1,B2,C1,C2'],
            'id_user' => ['nullable', 'exists:users,id'],
        ];
    }
}
