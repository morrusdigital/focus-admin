@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Edit Client</h4>
        <a class="btn btn-light" href="{{ route('clients.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('clients.update', $client) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('clients._form', ['client' => $client])
                <button class="btn btn-primary" type="submit">Update</button>
            </form>
        </div>
    </div>
@endsection
