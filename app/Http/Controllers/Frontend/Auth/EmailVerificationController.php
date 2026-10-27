<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function show(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('account.appointments'))
            : view('frontend.auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('account.appointments'));
        }

        if ($request->user()->markEmailAsVerified()) {
            // Trigger core verified event if needed
        }

        return redirect()->intended(route('account.appointments'))->with('verified', true);
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->validate(['g-recaptcha-response' => 'required|captcha']);

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('account.appointments'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
