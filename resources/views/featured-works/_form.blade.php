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
    <label class="form-label" for="sector_slug">Sector Slug</label>
    <input class="form-control" id="sector_slug" name="sector_slug" type="text" value="{{ old('sector_slug', $featuredWork->sector_slug ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label" for="sector_label">Sector Label</label>
    <input class="form-control" id="sector_label" name="sector_label" type="text" value="{{ old('sector_label', $featuredWork->sector_label ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label" for="title">Title</label>
    <input class="form-control" id="title" name="title" type="text" value="{{ old('title', $featuredWork->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $featuredWork->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label" for="cta_label">CTA Label</label>
    <input class="form-control" id="cta_label" name="cta_label" type="text" value="{{ old('cta_label', $featuredWork->cta_label ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label" for="cta_url">CTA URL</label>
    <input class="form-control" id="cta_url" name="cta_url" type="text" value="{{ old('cta_url', $featuredWork->cta_url ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label" for="sort_order">Sort Order</label>
    <input class="form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $featuredWork->sort_order ?? 0) }}">
</div>

<div class="mb-3">
    <label class="form-label" for="image">Image</label>
    <input class="form-control" id="image" name="image" type="file" accept="image/*">
    @if (!empty($featuredWork->image_url))
        <div class="mt-2">
            <img class="img-fluid rounded" style="max-height: 140px;" src="{{ $featuredWork->image_url }}" alt="{{ $featuredWork->title ?? 'Preview' }}">
        </div>
    @endif
</div>

@php
    $isActive = old('is_active', $featuredWork->is_active ?? true);
@endphp
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($isActive)>
    <label class="form-check-label" for="is_active">Active</label>
</div>
