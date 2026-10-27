@php
    $days = ['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'];
@endphp

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead>
        <tr>
            <th>Day</th>
            <th>Start</th>
            <th>End</th>
            <th>Break start</th>
            <th>Break end</th>
            <th>Day off</th>
        </tr>
        </thead>
        <tbody>
        @foreach($days as $key => $label)
            @php
                $dayData = $hours[$key] ?? [];
                // Check if it's explicitly marked as a day off, or default Sunday to off
                $isOff = isset($dayData['is_day_off']) ? $dayData['is_day_off'] : ($key === 'sun');
            @endphp
            <tr>
                <td><strong>{{ $label }}</strong></td>

                {{-- Notice the exact name structure: hours[mon][start_time] --}}
                <td><input type="text" class="form-control form-control-sm" name="hours[{{ $key }}][start_time]" value="{{ $dayData['start_time'] ?? '' }}" {{ $isOff ? 'disabled' : '' }}></td>
                <td><input type="text" class="form-control form-control-sm" name="hours[{{ $key }}][end_time]" value="{{ $dayData['end_time'] ?? '' }}" {{ $isOff ? 'disabled' : '' }}></td>
                <td><input type="text" class="form-control form-control-sm" name="hours[{{ $key }}][break_start_time]" value="{{ $dayData['break_start_time'] ?? '' }}" {{ $isOff ? 'disabled' : '' }}></td>
                <td><input type="text" class="form-control form-control-sm" name="hours[{{ $key }}][break_end_time]" value="{{ $dayData['break_end_time'] ?? '' }}" {{ $isOff ? 'disabled' : '' }}></td>

                <td class="text-center">
                    {{-- Hidden fallback so unchecked boxes still submit a '0' value --}}
                    <input type="hidden" name="hours[{{ $key }}][is_day_off]" value="0">
                    <input class="form-check-input" type="checkbox" name="hours[{{ $key }}][is_day_off]" value="1" {{ $isOff ? 'checked' : '' }}>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
