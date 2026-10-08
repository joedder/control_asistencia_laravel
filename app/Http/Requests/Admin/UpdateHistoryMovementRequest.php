<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHistoryMovementRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'id_group' => ['required', 'integer', 'exists:groups,id'],
            'id_new_group' => ['required', 'integer', 'exists:groups,id', 'different:id_group'],
            'students' => ['required', 'array', 'min:1'],
            'students.*' => ['integer', 'exists:students,id'],
            'migrated' => ['boolean'],
        ];
    }
}
