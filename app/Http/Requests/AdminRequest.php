<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class AdminRequest extends BaseApiRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:admins,name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email'],
            "username" => ['required', 'string', 'max:255', 'unique:admins,username'],
            'password' => ['required', 'string', 'min:8', 'max:20'], // , 'confirmed'
            'mobile_number' => ['required', 'string', 'max:255', 'unique:admins,mobile_number'],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'name.unique' => 'Name has already been taken',
            'email.required' => 'Email is required',
            'email.string' => 'Email must be a string',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 255 characters',
            'email.unique' => 'Email has already been taken',
            'mobile_number.required' => 'Mobile number is required',
            'mobile_number.string' => 'Mobile number must be a string',
            'mobile_number.max' => 'Mobile number must not exceed 255 characters',
            'mobile_number.unique' => 'Mobile number has already been taken',
        ];
    }
}
