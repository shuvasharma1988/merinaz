@extends('layouts.admin.admin')

@section('title', 'Messages')

@section('content')
    <section class="mz-view on" id="view-messages" aria-label="Contact messages">
        <div class="view-head">
            <div>
                <h1>Messages</h1>
                <p class="muted small mb-0">Enquiries sent through the website contact form</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.messages.index') }}" class="row g-2 mb-3">
            <div class="col-md-4">
                <input class="form-control form-control-sm" type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email or message…">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="status">
                    <option value="">All statuses</option>
                    @foreach(['new' => 'New', 'read' => 'Read', 'in_progress' => 'In progress', 'replied' => 'Replied', 'archived' => 'Archived'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline btn-sm" type="submit">Filter</button>
                <a href="{{ route('admin.messages.index') }}" class="btn btn-quiet btn-sm">Reset</a>
            </div>
        </form>

        <div class="tone">
            @forelse($messages as $message)
                <div class="line-item">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <strong>{{ $message->name }}</strong>
                            <div class="tiny muted mt-1">
                                {{ $message->email }}
                                @if($message->phone)
                                    · {{ $message->phone }}
                                @endif
                                · {{ $message->topic ?? 'General enquiry' }}
                                · {{ $message->created_at?->diffForHumans() }}
                            </div>
                        </div>
                        <span class="st st-{{ $message->status === 'new' ? 'pending' : 'confirmed' }}">
                            <i></i><span>{{ str_replace('_', ' ', ucfirst($message->status)) }}</span>
                        </span>
                    </div>

                    <p class="small mt-2 mb-0">{{ \Illuminate\Support\Str::limit($message->message, 180) }}</p>

                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-quiet btn-sm">Open</a>
                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="small muted mb-0">No messages yet.</p>
            @endforelse
        </div>

        @if($messages->hasPages())
            <div class="mt-3">{{ $messages->links() }}</div>
        @endif
    </section>
@endsection
