<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('frontend.contact');
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'topic' => $data['topic'] ?? null,
            'message' => $data['message'],
            'status' => ContactMessage::STATUS_NEW,
        ]);

        return redirect()
            ->route('contact')
            ->with('success', 'Thanks — your message is in. We reply within one business day.');
    }
}
