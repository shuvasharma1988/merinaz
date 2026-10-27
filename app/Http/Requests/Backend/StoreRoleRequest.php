<?php

namespace App\Http\Requests\Backend;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Role::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = auth()->user();

            // Super-users bypass the delegation constraint
            if ($user->hasRole('owner')) {
                return;
            }

            $requestedPermissions = $this->input('permissions', []);
            $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();

            // Verifies the delegator actually holds every permission they are trying to attach to the new role
            $unauthorizedDelegation = array_diff($requestedPermissions, $userPermissions);

            if (! empty($unauthorizedDelegation)) {
                $validator->errors()->add('permissions', 'Security constraint: You cannot assign permissions to a role that you do not personally possess.');
            }
        });
    }
}
