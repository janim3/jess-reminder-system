@extends('layouts.app')

@section('content')
<h2 class="mb-4">Template: {{ $template->name }}</h2>
<dl class="row">
    <dt class="col-sm-3">Content</dt>
    <dd class="col-sm-9"><pre class="bg-light p-3 rounded">{{ $template->content }}</pre></dd>
    <dt class="col-sm-3">Created</dt>
    <dd class="col-sm-9">{{ $template->created_at->format('Y-m-d H:i') }}</dd>
</dl>
<a href="{{ route('templates.edit', $template) }}" class="btn btn-warning">Edit</a>
<a href="{{ route('templates.index') }}" class="btn btn-secondary">Back</a>
@endsection
