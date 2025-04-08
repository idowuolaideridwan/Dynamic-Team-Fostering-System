<?php

namespace App\Http\Requests\API\V1\GradeRequest;

use Illuminate\Foundation\Http\FormRequest;

class GetStudentAveragesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'students' => ['sometimes', 'array'],
            'students.*' => ['string', 'regex:/^\d{6}[A-Z]$/i'], 
            'summary_only' => ['nullable', 'in:true,false,1,0,on,off'],
        ];
    }

    public function messages(): array
    {
        return [
            'students.*.regex' => 'Each student ID must be a 6-digit number followed by a capital letter (e.g., 123021S).',
        ];
    }
}
