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

document.addEventListener('DOMContentLoaded', function () {
    const freq = document.getElementById('frequency_type').value;
    if (freq) updateTimePickers();
});
</script>
@endsection
