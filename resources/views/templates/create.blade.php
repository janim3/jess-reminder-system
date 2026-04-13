@extends('layouts.app')

@section('content')
<h2 class="mb-4">Add Template</h2>
<p class="text-muted">Use <code>{name}</code> to insert the contact's name.</p>

<form action="{{ route('templates.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Content <span class="text-danger">*</span></label>
        <textarea name="content" rows="5" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('templates.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
