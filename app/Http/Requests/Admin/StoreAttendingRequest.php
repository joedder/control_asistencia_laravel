<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendingRequest extends FormRequest
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
            'id_teacher' => ['required', 'integer', 'exists:teachers,id'],
            'id_student' => ['required', 'integer', 'exists:students,id'],
            'id_group' => ['required', 'integer', 'exists:groups,id'],
            'status' => ['required', 'in:asistente,inasistente,justificado'],
            'social_reason' => ['nullable', 'string'],
            'class_date' => ['required', 'date'],
        ];
    }
}
