@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Edit Project</h4>
        <a class="btn btn-light" href="{{ route('projects.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('projects._form', ['project' => $project])
                <button class="btn btn-primary" type="submit">Update</button>
            </form>
        </div>
    </div>
@endsection
