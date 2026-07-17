<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guard('student')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
//            'answer' => ['nullable', 'string', 'max:5000'],

            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,zip,doc,docx,png,jpg,jpeg'
            ],

            // optional if later you support link submissions
            'link' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
//            'answer.nullable' => 'Answer is required',
//            'answer.string' => 'Answer is not valid',
//            'answer.max' => 'Answer is too long',
//            'answer.file' => 'Answer is too large',
//            'answer.mimes' => 'Answer is not valid',
            'file.mimes' => 'Only PDF, ZIP, images or docs are allowed.',
            'file.max' => 'File size must not exceed 10MB.',
            'link.url' => 'Link is not valid',
        ];
    }
}
