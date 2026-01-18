@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Add Project</h4>
        <a class="btn btn-light" href="{{ route('projects.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                @csrf
                @include('projects._form')
                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
@endsection
