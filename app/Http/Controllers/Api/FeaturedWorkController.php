<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeaturedWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeaturedWorkController extends Controller
{
    public function index()
    {
        $data = FeaturedWork::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (FeaturedWork $featuredWork) => $this->payload($featuredWork));

        return response()->json([
            'data' => $data,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        $items = FeaturedWork::orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'data' => collect($items->items())->map(fn (FeaturedWork $featuredWork) => $this->payload($featuredWork))->values(),
            'meta' => [
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('featured-work', 'public');
        }

        $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        $validated['is_active'] = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        $featuredWork = FeaturedWork::create($validated);

        return response()->json([
            'data' => $this->payload($featuredWork),
        ], 201);
    }

    public function show(FeaturedWork $featuredWork)
    {
        return response()->json([
            'data' => $this->payload($featuredWork),
        ]);
    }

    public function update(Request $request, FeaturedWork $featuredWork)
    {
        $validated = $request->validate($this->updateRules());

        if ($request->hasFile('image')) {
            $this->deleteImageIfLocal($featuredWork->image);
            $validated['image'] = $request->file('image')->store('featured-work', 'public');
        }

        if ($request->has('sort_order')) {
            $validated['sort_order'] = $this->normalizeSortOrder($request->input('sort_order'));
        }

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $featuredWork->update($validated);

        return response()->json([
            'data' => $this->payload($featuredWork->fresh()),
        ]);
    }

    public function destroy(FeaturedWork $featuredWork)
    {
        $this->deleteImageIfLocal($featuredWork->image);
        $featuredWork->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'sector_slug' => ['required', 'string', 'max:120'],
            'sector_label' => ['required', 'string', 'max:150'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'cta_label' => ['nullable', 'string', 'max:150'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function updateRules(): array
    {
        return [
            'sector_slug' => ['sometimes', 'required', 'string', 'max:120'],
            'sector_label' => ['sometimes', 'required', 'string', 'max:150'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'cta_label' => ['sometimes', 'nullable', 'string', 'max:150'],
            'cta_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
        ];
    }

    private function normalizeSortOrder(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) $value;
    }

    private function deleteImageIfLocal(?string $image): void
    {
        if (!$image) {
            return;
        }

        if (Str::startsWith($image, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk('public')->delete($image);
    }

    private function payload(FeaturedWork $featuredWork): array
    {
        return [
            'id' => $featuredWork->id,
            'sector_slug' => $featuredWork->sector_slug,
            'sector_label' => $featuredWork->sector_label,
            'title' => $featuredWork->title,
            'description' => $featuredWork->description,
            'cta_label' => $featuredWork->cta_label,
            'cta_url' => $featuredWork->cta_url,
            'image' => $featuredWork->image,
            'image_url' => $featuredWork->image_url,
            'sort_order' => $featuredWork->sort_order,
            'is_active' => (bool) $featuredWork->is_active,
            'created_at' => $featuredWork->created_at?->toDateTimeString(),
            'updated_at' => $featuredWork->updated_at?->toDateTimeString(),
        ];
    }
}
