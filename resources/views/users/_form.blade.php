@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input
        class="form-control"
        id="name"
        name="name"
        type="text"
        value="{{ old('name', $user->name ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input
        class="form-control"
        id="email"
        name="email"
        type="email"
        value="{{ old('email', $user->email ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label" for="password">Password</label>
    <input
        class="form-control"
        id="password"
        name="password"
        type="password"
        @if (empty($user->id)) required @endif
    >
    @if (!empty($user->id))
        <small class="text-muted">Leave blank to keep current password.</small>
    @endif
</div>
