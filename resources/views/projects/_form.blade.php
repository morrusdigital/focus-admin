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
    <label class="form-label" for="sector">Sector</label>
    <input
        class="form-control"
        id="sector"
        name="sector"
        type="text"
        value="{{ old('sector', $project->sector ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label class="form-label" for="images">Images</label>
    <div id="project-images-inputs" class="d-flex flex-column gap-2">
        <input
            class="form-control project-images-input"
            id="images"
            name="images[]"
            type="file"
            accept="image/*"
            multiple
            @if (empty($project->images) || $project->images->isEmpty()) required @endif
        >
    </div>
    <div class="d-flex align-items-center gap-2 mt-2">
        <button class="btn btn-outline-secondary btn-sm" type="button" id="add-project-image">
            + Add Image
        </button>
        <small class="text-muted">Max 2MB per image.</small>
    </div>
    <div id="project-images-preview" class="row g-2 mt-2"></div>
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
        <label class="form-label">Current Images</label>
        <div class="row g-2">
            @foreach ($project->images as $image)
                <div class="col-md-3">
                    <div class="border rounded p-2 h-100 text-center">
                        <img class="img-fluid rounded mb-2" style="max-height: 120px;" src="{{ $image->image_url }}" alt="{{ $project->title }}">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remove_image_{{ $image->id }}" name="remove_image_ids[]" value="{{ $image->id }}">
                            <label class="form-check-label" for="remove_image_{{ $image->id }}">Remove</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
<script>
    (() => {
        const previewContainer = document.getElementById('project-images-preview');
        const inputsContainer = document.getElementById('project-images-inputs');
        const addButton = document.getElementById('add-project-image');

        if (!previewContainer || !inputsContainer || !addButton) {
            return;
        }

        const renderPreviews = () => {
            const inputs = inputsContainer.querySelectorAll('.project-images-input');
            previewContainer.innerHTML = '';

            inputs.forEach((input) => {
                const files = Array.from(input.files || []);
                files.forEach((file) => {
                    const url = URL.createObjectURL(file);
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4';

                    const img = document.createElement('img');
                    img.className = 'img-fluid rounded border';
                    img.style.maxHeight = '140px';
                    img.alt = file.name;
                    img.src = url;
                    img.onload = () => URL.revokeObjectURL(url);

                    col.appendChild(img);
                    previewContainer.appendChild(col);
                });
            });
        };

        const addInput = () => {
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'images[]';
            input.accept = 'image/*';
            input.className = 'form-control project-images-input';
            input.addEventListener('change', renderPreviews);
            inputsContainer.appendChild(input);
        };

        inputsContainer.querySelectorAll('.project-images-input').forEach((input) => {
            input.addEventListener('change', renderPreviews);
        });

        addButton.addEventListener('click', addInput);
    })();
</script>
@endpush
