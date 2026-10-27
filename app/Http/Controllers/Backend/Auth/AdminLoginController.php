<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Events\Auth\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        if (! Auth::user()->isAdmin()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('You do not have administrative access.'),
            ]);
        }

        $request->session()->regenerate();
        event(new UserLoggedIn(Auth::user(), $request->ip(), $request->userAgent()));

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
