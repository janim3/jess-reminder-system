@extends('layouts.app')

@section('content')
<h2 class="mb-4">Add Assignment</h2>

<form action="{{ route('assignments.store') }}" method="POST" id="assignmentForm">
    @csrf
    <div class="mb-3">
        <label class="form-label">Contact <span class="text-danger">*</span></label>
        <select name="contact_id" class="form-select @error('contact_id') is-invalid @enderror">
            <option value="">-- Select Contact --</option>
            @foreach ($contacts as $contact)
                <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>
                    {{ $contact->name }} ({{ $contact->phone_number }})
                </option>
            @endforeach
        </select>
        @error('contact_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Template <span class="text-danger">*</span></label>
        <select name="template_id" class="form-select @error('template_id') is-invalid @enderror">
            <option value="">-- Select Template --</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                    {{ $template->name }}
                </option>
            @endforeach
        </select>
        @error('template_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Schedule Mode <span class="text-danger">*</span></label>
        @php $scheduleMode = old('schedule_mode', 'simple'); @endphp
        <div class="form-check">
            <input class="form-check-input" type="radio" name="schedule_mode" id="mode_simple" value="simple" {{ $scheduleMode === 'simple' ? 'checked' : '' }}>
            <label class="form-check-label" for="mode_simple">Simple (preset frequency)</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="schedule_mode" id="mode_advanced" value="advanced" {{ $scheduleMode === 'advanced' ? 'checked' : '' }}>
            <label class="form-check-label" for="mode_advanced">Advanced (custom recurrence)</label>
        </div>
    </div>

    <div class="mb-3 simple-only">
        <label class="form-label">Frequency <span class="text-danger">*</span></label>
        <select name="frequency_type" id="frequency_type" class="form-select @error('frequency_type') is-invalid @enderror" onchange="updateTimePickers()">
            <option value="">-- Select Frequency --</option>
            <option value="daily_once" {{ old('frequency_type') == 'daily_once' ? 'selected' : '' }}>Daily Once</option>
            <option value="daily_twice" {{ old('frequency_type') == 'daily_twice' ? 'selected' : '' }}>Daily Twice</option>
            <option value="daily_thrice" {{ old('frequency_type') == 'daily_thrice' ? 'selected' : '' }}>Daily Thrice</option>
            <option value="weekly" {{ old('frequency_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
        </select>
        @error('frequency_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3" id="sendTimesContainer">
        <label class="form-label">Send Times <span class="text-danger">*</span></label>
        <div id="timePickers">
            <input type="time" name="send_times[]" class="form-control mb-2" value="{{ old('send_times.0', '09:00') }}">
        </div>
        @error('send_times')<div class="text-danger small">{{ $message }}</div>@enderror
        @error('send_times.*')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div id="advancedOptions" class="border rounded p-3 mb-3 advanced-only">
        <h6 class="mb-3">Advanced Recurrence</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Repeat Unit</label>
                <select name="recurrence_unit" id="recurrence_unit" class="form-select @error('recurrence_unit') is-invalid @enderror">
                    <option value="seconds" {{ old('recurrence_unit') === 'seconds' ? 'selected' : '' }}>Seconds</option>
                    <option value="minutes" {{ old('recurrence_unit') === 'minutes' ? 'selected' : '' }}>Minutes</option>
                    <option value="hours" {{ old('recurrence_unit') === 'hours' ? 'selected' : '' }}>Hours</option>
                    <option value="daily" {{ old('recurrence_unit', 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ old('recurrence_unit') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ old('recurrence_unit') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ old('recurrence_unit') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
                @error('recurrence_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Every</label>
                <input type="number" min="1" max="365" name="recurrence_interval" class="form-control @error('recurrence_interval') is-invalid @enderror" value="{{ old('recurrence_interval', 1) }}">
                @error('recurrence_interval')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Timezone</label>
                <input type="text" name="timezone" class="form-control @error('timezone') is-invalid @enderror" value="{{ old('timezone', config('app.timezone', 'UTC')) }}" placeholder="Africa/Accra">
                @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', now()->toDateString()) }}">
                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">End Date (optional)</label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div id="weeklyFields" class="mt-3">
            <label class="form-label d-block">Days of Week</label>
            @php $oldDays = old('recurrence_days_of_week', [1]); @endphp
            @foreach ([1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'] as $key => $label)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="{{ $key }}" id="dow_{{ $key }}" {{ in_array($key, $oldDays) ? 'checked' : '' }}>
                    <label class="form-check-label" for="dow_{{ $key }}">{{ $label }}</label>
                </div>
            @endforeach
            @error('recurrence_days_of_week')<div class="text-danger small">{{ $message }}</div>@enderror
            @error('recurrence_days_of_week.*')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div id="dayOfMonthField" class="mt-3">
            <label class="form-label">Day of Month</label>
            <input type="number" min="1" max="31" name="recurrence_day_of_month" class="form-control @error('recurrence_day_of_month') is-invalid @enderror" value="{{ old('recurrence_day_of_month', 1) }}">
            @error('recurrence_day_of_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div id="monthOfYearField" class="mt-3">
            <label class="form-label">Month of Year</label>
            <input type="number" min="1" max="12" name="recurrence_month_of_year" class="form-control @error('recurrence_month_of_year') is-invalid @enderror" value="{{ old('recurrence_month_of_year', 1) }}">
            @error('recurrence_month_of_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Channel <span class="text-danger">*</span></label>
        <select name="channel" class="form-select @error('channel') is-invalid @enderror">
            <option value="">-- Select Channel --</option>
            <option value="sms" {{ old('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
            <option value="email" {{ old('channel') == 'email' ? 'selected' : '' }}>Email</option>
            <option value="both" {{ old('channel') == 'both' ? 'selected' : '' }}>Both</option>
        </select>
        @error('channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection

@section('scripts')
<script>
const timeCounts = { daily_once: 1, daily_twice: 2, daily_thrice: 3, weekly: 1 };

function updateTimePickers() {
    const freq = document.getElementById('frequency_type').value;
    const count = timeCounts[freq] || 1;
    const container = document.getElementById('timePickers');
    container.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const input = document.createElement('input');
        input.type = 'time';
        input.name = 'send_times[]';
        input.className = 'form-control mb-2';
        input.value = '09:00';
        container.appendChild(input);
    }
}

function updateScheduleMode() {
    const mode = document.querySelector('input[name="schedule_mode"]:checked')?.value || 'simple';
    document.querySelectorAll('.simple-only').forEach(el => el.style.display = mode === 'simple' ? '' : 'none');
    document.querySelectorAll('.advanced-only').forEach(el => el.style.display = mode === 'advanced' ? '' : 'none');
}

function updateAdvancedFieldsVisibility() {
    const unit = document.getElementById('recurrence_unit').value;
    document.getElementById('weeklyFields').style.display = unit === 'weekly' ? '' : 'none';
    document.getElementById('dayOfMonthField').style.display = ['monthly', 'yearly'].includes(unit) ? '' : 'none';
    document.getElementById('monthOfYearField').style.display = unit === 'yearly' ? '' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const freq = document.getElementById('frequency_type').value;
    if (freq) {
        updateTimePickers();
    }

    updateScheduleMode();
    updateAdvancedFieldsVisibility();

    document.querySelectorAll('input[name="schedule_mode"]').forEach(el => {
        el.addEventListener('change', updateScheduleMode);
    });
    document.getElementById('recurrence_unit').addEventListener('change', updateAdvancedFieldsVisibility);
});
</script>
@endsection
