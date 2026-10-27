@extends('layouts.admin.admin')

@section('title', 'Visit ' . $booking->booking_reference)

@section('content')
    <section class="mz-view on" id="view-booking-detail" aria-label="Booking detail">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to bookings
        </a>

        @if(session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        <div class="view-head">
            <div>
                <h1>Visit {{ $booking->booking_reference }} · {{ $booking->client->name }}</h1>
                <p class="muted small mb-0">
                    {{ $booking->appointment_date->format('D j M Y') }} at {{ $booking->location->name }} ·
                    <span class="st st-{{ strtolower(str_replace('_', '-', $booking->status)) }}">
                        <i></i><span>{{ str_replace('_', ' ', strtolower($booking->status)) }}</span>
                    </span>
                </p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-outline btn-sm">
                    Modify Visit Services
                </a>
            </div>
        </div>

        <div class="detail-grid">
            {{-- Left Details Column --}}
            <div>
                <h2>Status & Next Steps</h2>
                <div class="tone section-gap">
                    <p class="small muted mb-2">Update the operational status of this visit. Completed visits cannot be reopened.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('admin.bookings.status.update', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="checked_in">
                            <button type="submit" class="btn btn-outline btn-sm" {{ in_array($booking->status, ['checked_in', 'in_progress', 'completed', 'cancelled', 'no_show']) ? 'disabled' : '' }}>
                                Client Checked-in
                            </button>
                        </form>

                        <form action="{{ route('admin.bookings.status.update', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-outline btn-sm" {{ in_array($booking->status, ['in_progress', 'completed', 'cancelled', 'no_show']) ? 'disabled' : '' }}>
                                Service Started
                            </button>
                        </form>

                        <form action="{{ route('admin.bookings.status.update', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-ink btn-sm" {{ in_array($booking->status, ['completed', 'cancelled', 'no_show']) ? 'disabled' : '' }}>
                                Complete Visit
                            </button>
                        </form>

                        <form action="{{ route('admin.bookings.status.update', $booking) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="no_show">
                            <button type="submit" class="btn btn-danger-line btn-sm" {{ in_array($booking->status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                                Mark No-show
                            </button>
                        </form>
                    </div>
                </div>

                <h2>Services in this visit</h2>
                @foreach($booking->items as $item)
                    <div class="line-item tone mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>{{ $item->service->name }}</strong>
                            <span class="small fw-bold">${{ number_format($item->price, 2) }}</span>
                        </div>
                        <div class="row mt-2 g-2">
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Assigned Staff</label>
                                <div>{{ $item->staff->name }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Start</label>
                                <div>{{ $item->start_time->format('g:i A') }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">End</label>
                                <div>{{ $item->end_time->format('g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <h2 class="mt-4">Staff notes on this visit</h2>
                <div class="tone section-gap">
                    <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                        @csrf
                        @method('PUT')
                        {{-- Hidden payloads required to bypass the multi-service array validation when just saving notes --}}
                        <input type="hidden" name="location_id" value="{{ $booking->location_id }}">
                        <input type="hidden" name="appointment_date" value="{{ $booking->appointment_date->format('Y-m-d') }}">
                        @foreach($booking->items as $index => $item)
                            <input type="hidden" name="items[{{$index}}][service_id]" value="{{ $item->service_id }}">
                            <input type="hidden" name="items[{{$index}}][staff_id]" value="{{ $item->staff_id }}">
                            <input type="hidden" name="items[{{$index}}][start_time]" value="{{ $item->start_time->format('H:i') }}">
                        @endforeach

                        <label class="form-label" for="staff_notes">Visible only to admin and staff</label>
                        <textarea class="form-control" id="staff_notes" name="staff_notes" rows="3">{{ $booking->staff_notes }}</textarea>

                        <button type="submit" class="btn btn-ink btn-sm mt-2">Save notes</button>
                    </form>
                </div>
            </div>

            {{-- Right Column: Summary & Cancellation Policies --}}
            <div>
                <div class="tone section-gap">
                    <h3>Client Profile</h3>
                    <p class="mb-1"><strong>{{ $booking->client->name }}</strong></p>
                    <p class="small muted mb-1">{{ $booking->client->email }}</p>
                    @if($booking->customer_notes)
                        <div class="mt-2 p-2 border rounded bg-warning bg-opacity-10">
                            <strong class="small d-block text-warning-emphasis">Client Note:</strong>
                            <span class="small">{{ $booking->customer_notes }}</span>
                        </div>
                    @endif
                </div>

                <div class="tone section-gap">
                    <h3>Invoice Summary</h3>
                    <div class="d-flex justify-content-between small mb-1 text-muted">
                        <span>Services Subtotal</span>
                        <span>${{ number_format($booking->subtotal, 2) }}</span>
                    </div>
                    @if($booking->discount > 0)
                        <div class="d-flex justify-content-between small mb-1 text-success">
                            <span>Discount Applied</span>
                            <span>-${{ number_format($booking->discount, 2) }}</span>
                        </div>
                    @endif
                    @if($booking->tax > 0)
                        <div class="d-flex justify-content-between small mb-1 text-muted">
                            <span>Tax</span>
                            <span>+${{ number_format($booking->tax, 2) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                        <strong>Total Due</strong>
                        <strong>${{ number_format($booking->total, 2) }}</strong>
                    </div>
                </div>

                <div class="policy-box">
                    <h3>Cancel this visit</h3>
                    <p class="mb-2">Cancelling this appointment will release all {{ $booking->items->count() }} service time slots back to the calendar.</p>

                    <form action="{{ route('admin.bookings.status.update', $booking) }}" method="POST" onsubmit="return confirm('Cancel this entire visit?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger-line btn-sm w-100" {{ in_array($booking->status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                            Cancel Entire Visit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
