@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Featured Projects</h4>
            <small class="text-muted">Drag the handle to reorder.</small>
        </div>
        <a class="btn btn-primary" href="{{ route('featured-projects.create') }}">Add Project</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Order</th>
                            <th>Title</th>
                            <th>Scope</th>
                            <th>Size</th>
                            <th>Image</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="featured-projects-sortable">
                        @forelse ($projects as $project)
                            <tr data-id="{{ $project->id }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="drag-handle text-muted" style="cursor: grab;" draggable="true" title="Drag to reorder">&#9776;</span>
                                        <span class="sort-order badge bg-light text-dark border">{{ $project->sort_order ?? '-' }}</span>
                                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Move row">
                                            <button type="button" class="btn btn-outline-secondary" data-move="up" title="Move up">
                                                <i class="ri-arrow-up-s-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" data-move="down" title="Move down">
                                                <i class="ri-arrow-down-s-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $project->title }}</td>
                                <td>{{ $project->scope }}</td>
                                <td>{{ $project->size }}</td>
                                <td>
                                    <a href="{{ $project->image_url }}" target="_blank" rel="noopener">View</a>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('featured-projects.show', $project) }}">Detail</a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('featured-projects.edit', $project) }}">Edit</a>
                                    <form class="d-inline" method="POST" action="{{ route('featured-projects.destroy', $project) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this project?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No projects yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('featured-projects-sortable');
    if (!tbody) {
        return;
    }

    let draggingRow = null;

    tbody.addEventListener('dragstart', function (event) {
        const handle = event.target.closest('.drag-handle');
        if (!handle) {
            event.preventDefault();
            return;
        }

        draggingRow = handle.closest('tr');
        if (!draggingRow) {
            return;
        }

        event.dataTransfer.setData('text/plain', draggingRow.dataset.id || '');
        draggingRow.classList.add('table-active');
        event.dataTransfer.effectAllowed = 'move';
    });

    tbody.addEventListener('dragend', function () {
        if (!draggingRow) {
            return;
        }

        draggingRow.classList.remove('table-active');
        draggingRow = null;
        updateOrder();
    });

    tbody.addEventListener('dragover', function (event) {
        event.preventDefault();
        const row = event.target.closest('tr');
        if (!row || row === draggingRow) {
            return;
        }

        const rect = row.getBoundingClientRect();
        const shouldInsertAfter = (event.clientY - rect.top) > rect.height / 2;
        tbody.insertBefore(draggingRow, shouldInsertAfter ? row.nextSibling : row);
    });

    tbody.addEventListener('click', function (event) {
        const button = event.target.closest('[data-move]');
        if (!button) {
            return;
        }

        const row = button.closest('tr');
        if (!row) {
            return;
        }

        if (button.dataset.move === 'up') {
            const prev = row.previousElementSibling;
            if (prev) {
                tbody.insertBefore(row, prev);
            }
        } else if (button.dataset.move === 'down') {
            const next = row.nextElementSibling;
            if (next) {
                tbody.insertBefore(next, row);
            }
        }

        updateOrder();
    });

    function updateOrder() {
        const ids = Array.from(tbody.querySelectorAll('tr[data-id]')).map(function (row, index) {
            const orderLabel = row.querySelector('.sort-order');
            if (orderLabel) {
                orderLabel.textContent = String(index + 1);
            }
            return Number(row.dataset.id);
        });

        fetch('{{ route('featured-projects.reorder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ order: ids }),
        }).catch(function () {
            alert('Failed to update order. Please reload and try again.');
        });
    }
});
</script>
@endpush
