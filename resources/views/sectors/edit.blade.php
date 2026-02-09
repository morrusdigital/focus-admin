@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Edit Sector</h4>
        <a class="btn btn-light" href="{{ route('sectors.index') }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('sectors.update', $sector) }}">
                @csrf
                @method('PUT')
                @include('sectors._form')
                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
@endsection
