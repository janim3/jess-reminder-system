@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Templates</h2>
    <a href="{{ route('templates.create') }}" class="btn btn-primary">+ Add Template</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Content Preview</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($templates as $template)
        <tr>
            <td>{{ $template->id }}</td>
            <td>{{ $template->name }}</td>
            <td>{{ Str::limit($template->content, 80) }}</td>
            <td>
                <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this template?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center">No templates yet.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $templates->links() }}
@endsection
