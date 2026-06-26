<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class FileRequest extends BaseApiRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'max:5'],
            'files.*' => ['required', 'file', 'mimes:pdf,doc,docx,jpeg,jpg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Files is required.',
            'files.*.required' => 'Files is required.',
            'files.*.mimes' => 'Files must be of type: pdf, doc, docx.',
            'files.*.max' => 'Files must be less than 5MB.',
        ];
    }
}
