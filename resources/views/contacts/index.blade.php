@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Contacts</h2>
    <a href="{{ route('contacts.create') }}" class="btn btn-primary">+ Add Contact</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Date of Birth</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($contacts as $contact)
        <tr>
            <td>{{ $contact->id }}</td>
            <td>{{ $contact->name }}</td>
            <td>{{ $contact->phone_number }}</td>
            <td>{{ $contact->email ?? '—' }}</td>
            <td>{{ $contact->date_of_birth?->format('Y-m-d') ?? '—' }}</td>
            <td>
                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this contact?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No contacts yet.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $contacts->links() }}
@endsection
