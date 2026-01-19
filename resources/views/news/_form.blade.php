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
        value="{{ old('title', $article->title ?? '') }}"
        required
    >
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="slug">Slug (optional)</label>
        <input
            class="form-control"
            id="slug"
            name="slug"
            type="text"
            value="{{ old('slug', $article->slug ?? '') }}"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="news_status">Status</label>
        <select class="form-select" id="news_status" name="status" required>
            @php
                $status = old('status', $article->status ?? 'draft');
            @endphp
            <option value="draft" @selected($status === 'draft')>Draft</option>
            <option value="published" @selected($status === 'published')>Published</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="category">Category</label>
        <input
            class="form-control"
            id="category"
            name="category"
            type="text"
            value="{{ old('category', $article->category ?? '') }}"
        >
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="author">Author</label>
        <input
            class="form-control"
            id="author"
            name="author"
            type="text"
            value="{{ old('author', $article->author ?? '') }}"
        >
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="tags">Tags (comma separated)</label>
    <input
        class="form-control"
        id="tags"
        name="tags"
        type="text"
        value="{{ old('tags', isset($article->tags) ? implode(', ', $article->tags ?? []) : '') }}"
    >
</div>

<div class="mb-3">
    <label class="form-label" for="excerpt">Excerpt</label>
    <textarea
        class="form-control"
        id="excerpt"
        name="excerpt"
        rows="3"
        required
    >{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label" for="cover_image">Cover Image</label>
    <input
        class="form-control"
        id="cover_image"
        name="cover_image"
        type="file"
        accept="image/*"
        @if (empty($article->cover_image)) required @endif
    >
    @if (!empty($article->cover_image_url))
        <div class="mt-2">
            <img class="img-fluid rounded" style="max-height: 140px;" src="{{ $article->cover_image_url }}" alt="{{ $article->title ?? 'Cover' }}">
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label" for="content-editor">Content</label>
    <div id="content-editor" class="form-control" style="min-height: 220px;"></div>
    <textarea id="content-initial" class="d-none">{!! old('content', $article->content ?? '') !!}</textarea>
    <input type="hidden" id="content" name="content" value="{{ old('content', $article->content ?? '') }}" required>
</div>

@push('styles')
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script>
        (() => {
            const editorEl = document.getElementById('content-editor');
            const contentInput = document.getElementById('content');
            const contentInitial = document.getElementById('content-initial');

            if (!editorEl || !contentInput) {
                return;
            }

            const quill = new Quill(editorEl, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            if (contentInitial && contentInitial.value) {
                quill.clipboard.dangerouslyPasteHTML(contentInitial.value);
            }

            const form = editorEl.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    contentInput.value = quill.root.innerHTML;
                });
            }
        })();
    </script>
@endpush
