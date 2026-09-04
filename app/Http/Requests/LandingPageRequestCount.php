<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LandingPageRequestCount extends FormRequest
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
            'graduates_count' => ['sometimes', 'integer', 'min:0'],
            'courses_count' => ['sometimes', 'integer', 'min:0'],
            'professional_trainer_count' => ['sometimes', 'integer', 'min:0'],
            'success_stories_count' => ['sometimes', 'integer', 'min:0'],
            'practical_projects_count' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
