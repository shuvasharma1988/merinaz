<div class="tone requires-owner-manager">
    <h3 class="mb-3">Client Notes Timeline</h3>

    {{-- Add Note Form --}}
        <form action="{{ route('admin.clients.notes.store') }}" method="POST" class="mb-4">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="mb-2">
                <textarea class="form-control @error('note') is-invalid @enderror" name="note" rows="3" placeholder="Add an internal observation, treatment note, or formula..." required>{{ old('note') }}</textarea>
                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <select name="visibility" class="form-select form-select-sm w-auto">
                    <option value="team">Team (All Staff)</option>
                    <option value="manager">Manager/Owner Only</option>
                    <option value="private">Private (Only Me)</option>
                </select>
                <button type="submit" class="btn btn-ink btn-sm">Post Note</button>
            </div>
        </form>

    {{-- Notes Timeline --}}
    <div class="notes-timeline" style="max-height: 400px; overflow-y: auto;">
        @forelse($notes as $note)
            <div class="note-card border-start border-3 px-3 py-2 mb-3 bg-white shadow-sm rounded-end
                {{ $note->visibility === 'private' ? 'border-danger' : ($note->visibility === 'manager' ? 'border-warning' : 'border-info') }}">

                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="small">{{ $note->staff->name ?? 'Unknown Staff' }}</strong>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill bg-light text-dark border" style="font-size: 0.65em;">
                            {{ ucfirst($note->visibility) }}
                        </span>
                        <span class="text-muted" style="font-size: 0.75em;">{{ $note->created_at->format('M d, Y h:ia') }}</span>
                    </div>
                </div>

                <p class="small mb-1 text-dark">{{ $note->note }}</p>

                @can('delete', $note)
                    <div class="text-end">
                        <form action="{{ route('admin.clients.notes.destroy', $note) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this note?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0 m-0 small text-decoration-none" style="font-size: 0.75rem;">Delete</button>
                        </form>
                    </div>
                @endcan
            </div>
        @empty
            <p class="small text-muted fst-italic">No internal notes recorded for this client yet.</p>
        @endforelse
    </div>
</div>
