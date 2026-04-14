@extends('layouts.app')

@section('content')
<h2 class="mb-4">Assignment #{{ $assignment->id }}</h2>
<dl class="row">
    <dt class="col-sm-3">Contact</dt><dd class="col-sm-9">{{ $assignment->contact->name }}</dd>
    <dt class="col-sm-3">Template</dt><dd class="col-sm-9">{{ $assignment->template->name }}</dd>
    <dt class="col-sm-3">Frequency</dt><dd class="col-sm-9">{{ $assignment->is_advanced ? 'Advanced' : str_replace('_', ' ', $assignment->frequency_type) }}</dd>
    <dt class="col-sm-3">Send Times</dt><dd class="col-sm-9">{{ implode(', ', $assignment->send_times) }}</dd>
    <dt class="col-sm-3">Channel</dt><dd class="col-sm-9"><span class="badge bg-info text-dark">{{ $assignment->channel }}</span></dd>
    <dt class="col-sm-3">Created</dt><dd class="col-sm-9">{{ $assignment->created_at->format('Y-m-d H:i') }}</dd>
</dl>
<a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-warning">Edit</a>
<a href="{{ route('assignments.index') }}" class="btn btn-secondary">Back</a>
@endsection
