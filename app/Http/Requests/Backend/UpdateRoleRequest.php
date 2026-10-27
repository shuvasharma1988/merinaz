<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->route('role');

        return Gate::allows('update', $role);
    }

    public function rules(): array
    {
        $roleId = $this->route('role')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            // Ensure slug remains unique, ignoring the current role's slug
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:roles,slug,'.$roleId],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            if ($user->hasRole('owner')) {
                return;
            }

            $requestedPermissions = $this->input('permissions', []);
            $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();

            $unauthorizedDelegation = array_diff($requestedPermissions, $userPermissions);

            if (! empty($unauthorizedDelegation)) {
                $validator->errors()->add('permissions', 'Security constraint: You cannot assign permissions to a role that you do not personally possess.');
            }
        });
    }
}
