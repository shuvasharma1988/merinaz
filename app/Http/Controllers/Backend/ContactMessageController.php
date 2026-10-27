<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\ContactMessageReplied;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ReplyContactMessageRequest;
use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ContactMessage::class);

        $query = ContactMessage::with('assignedStaff')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('message', 'like', '%'.$search.'%');
            });
        }

        $messages = $query->paginate(20)->withQueryString();

        return view('admin.contact.index', compact('messages'));
    }

    public function show(ContactMessage $message): View
    {
        Gate::authorize('view', $message);

        // Pass both $contactMessage and $message so either variable name works in Blade
        $contactMessage = $message;

        return view('admin.contact.details', compact('contactMessage', 'message'));
    }

    public function markAsRead(ContactMessage $message): RedirectResponse
    {
        Gate::authorize('view', $message);

        if ($message->status === ContactMessage::STATUS_NEW) {
            $message->update(['status' => ContactMessage::STATUS_READ]);
        }

        return redirect()
            ->route('admin.messages.show', $message)
            ->with('success', 'Message marked as read.');
    }

    public function reply(ReplyContactMessageRequest $request, ContactMessage $message): RedirectResponse
    {
        Gate::authorize('reply', $message);

        $validated = $request->validated();

        $message->update([
            'reply_message' => $validated['reply_message'],
            'assigned_to' => $validated['assigned_to'] ?? $request->user()->id,
            'status' => ContactMessage::STATUS_REPLIED,
            'replied_at' => now(),
        ]);

        Mail::to($message->email)->send(new ContactMessageReplyMail($message));

        event(new ContactMessageReplied($request->user(), $message));

        return redirect()
            ->route('admin.messages.show', $message)
            ->with('success', 'Reply sent to '.$message->email.' successfully.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        Gate::authorize('delete', $message);

        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('success', 'Message removed from inbox.');
    }
}
