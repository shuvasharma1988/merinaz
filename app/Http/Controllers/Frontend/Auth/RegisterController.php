<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Events\Auth\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ClientProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('frontend.auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            // 1. Create the core User record
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
            ]);

            // 2. Attach the Customer Role
            $customerRole = Role::where('slug', 'customer')->first();
            if ($customerRole) {
                $user->roles()->attach($customerRole);
            }

            // 3. Generate the linked ClientProfile
            ClientProfile::create([
                'user_id' => $user->id,
            ]);

            return $user;
        });

        event(new UserRegistered($user));

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful.');
    }
}
