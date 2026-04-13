@extends('layouts.app')

@section('content')
<h2 class="mb-4">Contact: {{ $contact->name }}</h2>
<dl class="row">
    <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $contact->phone_number }}</dd>
    <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $contact->email ?? '—' }}</dd>
    <dt class="col-sm-3">Date of Birth</dt><dd class="col-sm-9">{{ $contact->date_of_birth?->format('Y-m-d') ?? '—' }}</dd>
    <dt class="col-sm-3">Created</dt><dd class="col-sm-9">{{ $contact->created_at->format('Y-m-d H:i') }}</dd>
</dl>
<a href="{{ route('contacts.edit', $contact) }}" class="btn btn-warning">Edit</a>
<a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back</a>
@endsection
