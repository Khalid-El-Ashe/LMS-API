<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;

class RoleRequest extends BaseApiRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:roles,name' . $this->role?->id],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.unique' => 'Role is already exist.',
        ];
    }
}
