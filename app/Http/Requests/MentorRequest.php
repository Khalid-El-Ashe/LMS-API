<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class MentorRequest extends BaseApiRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:mentors,name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:mentors,email'],
            'country_iso' => ['required'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],

            'state_code' => ['required', 'string', 'size:2'],
            'experience' => ['nullable', 'numeric'],
//            'nationality' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
//            'status' => ['required', 'array', 'min:1', 'max:3'], // 'mentoring', 'opportunities', 'financial_support'
//            'status.*' => ['string', 'in:mentoring,opportunities,financial_support'],
            'course_id' => ['required', 'array'],
            'course_id.*' => ['exists:courses,id'],
        ];
    }

    public
    function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.unique' => 'This name already exists',
            'email.required' => 'Email is required',
            'email.unique' => 'This email already exists',
            'mobile_number.required' => 'Mobile number is required',
            'country_iso.required' => 'Country iso is required',
            'code_mobile.required' => 'Mobile number is required',
            'address.required' => 'Address is required',
            'city.required' => 'City is required',
            'state_code.required' => 'State is required',
//            'state_code.size' => 'State must be a valid code',
//            'experience.required' => 'Experience is required',
//            'nationality.required' => 'Nationality is required',
            'files.required' => 'Files is required',
            'files.mimes' => 'Files must be of type: jpeg, jpg, png',
            'files.max' => 'Max file size is 2 MB',
            'course_id.required' => 'Course is required',
        ];
    }
}
