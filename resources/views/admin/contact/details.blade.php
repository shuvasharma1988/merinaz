@extends('layouts.admin.admin')

@section('title', 'Message')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-message-show" aria-label="Contact message detail">
        <a href="{{ route('admin.messages.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to messages
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $contactMessage->name }}</h1>
                <p class="muted small mb-0">
                    {{ $contactMessage->email }}
                    @if($contactMessage->phone)
                        · {{ $contactMessage->phone }}
                    @endif
                    · {{ $contactMessage->created_at?->format('M j, Y g:i A') }}
                </p>
            </div>
            <div class="view-actions">
                @if($contactMessage->status === \App\Models\ContactMessage::STATUS_NEW)
                    <form action="{{ route('admin.messages.mark-as-read', $contactMessage) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Mark as read</button>
                    </form>
                @endif
                <form action="{{ route('admin.messages.destroy', $contactMessage) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                </form>
            </div>
        </div>

        <div class="detail-grid">
            <div>
                <div class="tone section-gap">
                    <h2>Message</h2>
                    <p class="small mb-0" style="white-space: pre-line;">{{ $contactMessage->message }}</p>
                </div>

                @if($contactMessage->reply_message)
                    <div class="tone section-gap">
                        <h2>Your reply</h2>
                        <p class="small mb-0" style="white-space: pre-line;">{{ $contactMessage->reply_message }}</p>
                        <p class="tiny muted mt-2 mb-0">
                            Replied {{ $contactMessage->replied_at?->format('M j, Y g:i A') }}
                            @if($contactMessage->assignedStaff)
                                · by {{ $contactMessage->assignedStaff->name }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <div>
                <div class="tone section-gap">
                    <h3>Details</h3>
                    <p class="small mb-1"><strong>Topic:</strong> {{ $contactMessage->topic ?? 'General enquiry' }}</p>
                    <p class="small mb-1">
                        <strong>Status:</strong>
                        <span class="st st-{{ $contactMessage->status === 'new' ? 'pending' : 'confirmed' }}">
                            <i></i><span>{{ str_replace('_', ' ', ucfirst($contactMessage->status)) }}</span>
                        </span>
                    </p>
                    @if($contactMessage->assignedStaff)
                        <p class="small mb-0"><strong>Assigned to:</strong> {{ $contactMessage->assignedStaff->name }}</p>
                    @endif
                </div>

                <div class="tone section-gap">
                    <h3>Reply</h3>
                    <form action="{{ route('admin.messages.reply', $contactMessage) }}" method="POST">
                        @csrf
                        <label class="form-label" for="replyMessage">Your reply</label>
                        <textarea class="form-control @error('reply_message') input-error @enderror"
                                  id="replyMessage"
                                  name="reply_message"
                                  rows="5"
                                  required>{{ old('reply_message') }}</textarea>
                        @error('reply_message') <div class="field-error">{{ $message }}</div> @enderror
                        <p class="tiny muted mt-1 mb-2">Sent to {{ $contactMessage->email }} from the studio address.</p>
                        <button type="submit" class="btn btn-accent btn-sm">Send reply</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
