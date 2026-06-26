<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;
use App\Models\Mentor;
use App\Models\Student;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LoginRequest extends BaseApiRequest
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
            'username' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username is required.',
        ];
    }
}
