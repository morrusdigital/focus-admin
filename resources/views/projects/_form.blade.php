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
    <label class="form-label" for="title">Title</label>
    <input
        class="form-control"
        id="title"
        name="title"
        type="text"
        value="{{ old('title', $project->title ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label">Sectors</label>
    @forelse ($sectors as $sector)
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                id="sector_{{ $sector->id }}"
                name="sectors[]"
                value="{{ $sector->id }}"
                @checked(in_array($sector->id, $selectedSectorIds))
            >
            <label class="form-check-label" for="sector_{{ $sector->id }}">
                {{ $sector->name }} ({{ $sector->slug }})
            </label>
        </div>
    @empty
        <p class="text-muted mb-0">No sectors available.</p>
    @endforelse
</div>

<div class="mb-3">
    <label class="form-label" for="image">Image</label>
    <input
        class="form-control"
        id="image"
        name="image"
        type="file"
        accept="image/*"
        @if (empty($project->images) || $project->images->isEmpty()) required @endif
    >
    <small class="text-muted">Max 2MB.</small>
</div>

@php
    $isActive = old('is_active', $project->is_active ?? true);
@endphp
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked($isActive)>
    <label class="form-check-label" for="is_active">Active</label>
</div>

@if (!empty($project->images) && $project->images->isNotEmpty())
    <div class="mb-3">
        <label class="form-label">Current Image</label>
        @php
            $currentImage = $project->images->first();
        @endphp
        @if ($currentImage)
            <div class="border rounded p-2 text-center" style="max-width: 200px;">
                <img class="img-fluid rounded" style="max-height: 160px;" src="{{ $currentImage->image_url }}" alt="{{ $project->title }}">
            </div>
        @endif
    </div>
@endif
