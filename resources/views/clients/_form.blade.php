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
        value="{{ old('name', $client->name ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label" for="sort_order">Sort Order</label>
    <input
        class="form-control"
        id="sort_order"
        name="sort_order"
        type="number"
        value="{{ old('sort_order', $client->sort_order ?? '') }}"
    >
</div>

<div class="mb-3">
    <label class="form-label" for="logo">Logo</label>
    <input
        class="form-control"
        id="logo"
        name="logo"
        type="file"
        accept="image/*"
        @if (empty($client->logo)) required @endif
    >
    @if (!empty($client->logo_url))
        <div class="mt-2">
            <img class="img-fluid rounded" style="max-height: 120px;" src="{{ $client->logo_url }}" alt="{{ $client->name ?? 'Logo' }}">
        </div>
    @endif
</div>

@php
    $isActive = old('is_active', $client->is_active ?? true);
@endphp
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($isActive)>
    <label class="form-check-label" for="is_active">Active</label>
</div>
