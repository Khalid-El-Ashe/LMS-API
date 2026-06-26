<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;

class PermissionRequest extends BaseApiRequest
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
            'name' => ['required', 'string', 'unique:permissions,name' . $this->permission?->id],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Permission name is required.',
            'name.string' => 'Permission name must be a string.',
            'name.unique' => 'Permission name must be unique.',
        ];
    }
}
