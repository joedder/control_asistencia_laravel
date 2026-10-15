<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'id_teacher' => [
                'required', 'integer',
                Rule::exists('teachers', 'id')->where(fn ($query) => $query->whereNotNull('id_user')),
            ],
            'substitute_teacher_id' => [
                'nullable', 'integer',
                Rule::exists('teachers', 'id')->where(fn ($query) => $query->whereNotNull('id_user')),
            ],
            'id_group' => ['required', 'integer', 'exists:groups,id'],
            'class_date' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*.id_student' => [
                'required', 'integer', 'distinct',
                Rule::exists('students', 'id')->where(fn ($query) => $query->where('id_group', $this->input('id_group'))),
            ],
            'attendances.*.status' => ['required', 'in:asistente,inasistente,justificado'],
            'attendances.*.social_reason' => ['nullable', 'string'],
        ];
    }
}
