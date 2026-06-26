<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StudentRequest extends BaseApiRequest
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
            'email' => ['required', 'email', 'unique:students,email'],
            'full_name' => ['required', 'string', 'min:5', 'max:255'],
            'university_name' => ['required', 'integer'],
            'university_major' => ['required', 'integer'],
            'country_iso' => ['required'],
            'mobile_number' => ['required', 'string', 'max:15'],
            'gender' => ['required', 'in:male,female'],
            'course_id' => ['required', 'array'],
            'course_id.*' => ['exists:courses,id'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('validation.email_required'),
            'email.email' => __('validation.email_invalid'),
            'email.unique' => __('validation.email_unique'),

            'full_name.required' => __('validation.full_name_required'),
            'full_name.string' => __('validation.full_name_string'),
            'full_name.min' => __('validation.full_name_min'),

            'university_name.required' => __('validation.university_name_required'),
            'university_major.required' => __('validation.university_major_required'),

            'country_iso.required' => __('validation.country_iso_required'),

            'mobile_number.required' => __('validation.mobile_required'),
            'mobile_number.string' => __('validation.mobile_string'),
            'mobile_number.max' => __('validation.mobile_max'),

            'course_id.required' => __('validation.course_id_required'),
            'course_id.array' => __('validation.course_id_array'),
            'course_id.*.exists' => __('validation.course_id_exists'),
        ];
    }
}
