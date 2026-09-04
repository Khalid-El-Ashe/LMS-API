<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SuccessStoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'story' => ['required', 'string'],
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ], // Required image upload
            'path' => ['nullable', 'string', 'max:255'], // Optional path
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'story.required' => 'The story field is required.',
            'image.image' => 'The image must be an image file.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
        ];
    }
}
