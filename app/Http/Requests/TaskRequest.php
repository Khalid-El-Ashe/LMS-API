<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends BaseApiRequest
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
//            'course_id' => ['required', 'exists:courses,id'],
            'video_id' => ['nullable', 'exists:course_videos,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'dead_line' => ['nullable', 'date'],
//            'is_required' => ['boolean'],
//            'type' => ['required', 'in:video,reading,quiz'],
//            'submission_type' => ['nullable', 'in:file,link,text'],
            'passing_grade' => ['nullable', 'integer', 'min:0', 'max:100'],
//            'order' => ['integer', 'min:0'],
        ];
    }


    public function messages(): array
    {
        return [
//            'course_id.required' => 'The course ID is required.',
//            'course_id.exists' => 'The selected course is invalid.',
            'video_id.exists' => 'The selected video is invalid.',
            'title.required' => 'The title is required.',
            'title.max' => 'The title may not exceed 255 characters.',
//            'is_required.boolean' => 'The is required field must be a boolean.',
//            'type.required' => 'The type is required.',
//            'type.in' => 'The selected type is invalid.',
//            'submission_type.in' => 'The selected submission type is invalid.',
            'passing_grade.integer' => 'The passing grade must be an integer.',
            'passing_grade.min' => 'The passing grade must be at least 0.',
            'passing_grade.max' => 'The passing grade may not exceed 100.',
//            'order.integer' => 'The order must be an integer.',
//            'order.min' => 'The order must be at least 0.',
        ];
    }
}
