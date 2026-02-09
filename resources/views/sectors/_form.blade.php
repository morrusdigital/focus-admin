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
        value="{{ old('name', $sector->name ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label" for="slug">Slug (optional)</label>
    <input
        class="form-control"
        id="slug"
        name="slug"
        type="text"
        value="{{ old('slug', $sector->slug ?? '') }}"
        placeholder="e.g. design-and-build"
    >
    <small class="text-muted">If empty, slug will be generated from name.</small>
</div>

<div class="mb-3">
    <label class="form-label" for="sort_order">Sort Order</label>
    <input
        class="form-control"
        id="sort_order"
        name="sort_order"
        type="number"
        value="{{ old('sort_order', $sector->sort_order ?? '') }}"
    >
</div>

@php
    $isActive = old('is_active', $sector->is_active ?? true);
@endphp
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($isActive)>
    <label class="form-check-label" for="is_active">Active</label>
</div>
