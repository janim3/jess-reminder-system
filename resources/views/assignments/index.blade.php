@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Assignments</h2>
    <a href="{{ route('assignments.create') }}" class="btn btn-primary">+ Add Assignment</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Contact</th>
            <th>Template</th>
            <th>Frequency</th>
            <th>Send Times</th>
            <th>Channel</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($assignments as $assignment)
        <tr>
            <td>{{ $assignment->id }}</td>
            <td>{{ $assignment->contact->name }}</td>
            <td>{{ $assignment->template->name }}</td>
            <td>{{ str_replace('_', ' ', $assignment->frequency_type) }}</td>
            <td>{{ implode(', ', $assignment->send_times) }}</td>
            <td><span class="badge bg-info text-dark">{{ $assignment->channel }}</span></td>
            <td>
                <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this assignment?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No assignments yet.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $assignments->links() }}
@endsection
