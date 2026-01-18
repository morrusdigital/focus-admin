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
    <label class="form-label" for="scope">Scope</label>
    <input
        class="form-control"
        id="scope"
        name="scope"
        type="text"
        value="{{ old('scope', $project->scope ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label" for="size">Size</label>
    <input
        class="form-control"
        id="size"
        name="size"
        type="text"
        value="{{ old('size', $project->size ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label" for="image">Image</label>
    <input
        class="form-control"
        id="image"
        name="image"
        type="file"
        accept="image/*"
        @if (empty($project->image)) required @endif
    >
    @if (!empty($project->image_url))
        <div class="mt-2">
            <img class="img-fluid rounded" style="max-height: 140px;" src="{{ $project->image_url }}" alt="{{ $project->title ?? 'Preview' }}">
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea
        class="form-control"
        id="description"
        name="description"
        rows="4"
        required
    >{{ old('description', $project->description ?? '') }}</textarea>
</div>
