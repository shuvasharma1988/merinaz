<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Events\Auth\PasswordReset as PasswordResetEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordEmailRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function createLinkRequest(): View
    {
        return view('frontend.auth.forgot-password');
    }

    public function storeLinkRequest(PasswordEmailRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function createReset(string $token): View
    {
        return view('frontend.auth.reset-password', ['token' => $token]);
    }

    public function storeReset(PasswordResetRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordResetEvent($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
