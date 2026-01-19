<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        return view('news.index', [
            'news' => $news,
            'title' => 'News',
            'subtitle' => 'Articles',
        ]);
    }

    public function create()
    {
        return view('news.create', [
            'article' => new News(),
            'title' => 'News',
            'subtitle' => 'Create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        $validated['slug'] = $this->resolveSlug(
            $validated['slug'] ?? '',
            $validated['title'] ?? ''
        );
        $validated['content'] = $this->sanitizeHtml($validated['content'] ?? '');
        $validated['tags'] = $this->normalizeTags($request->input('tags'));
        $validated['cover_image'] = $request->file('cover_image')->store('news', 'public');
        $validated['published_at'] = $this->resolvePublishedAt(
            $validated['status'] ?? 'draft'
        );

        News::create($validated);

        return redirect()
            ->route('news.index')
            ->with('success', 'Article created.');
    }

    public function show(News $news)
    {
        return view('news.show', [
            'article' => $news,
            'title' => 'News',
            'subtitle' => 'Detail',
        ]);
    }

    public function edit(News $news)
    {
        return view('news.edit', [
            'article' => $news,
            'title' => 'News',
            'subtitle' => 'Edit',
        ]);
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate($this->updateRules($news));

        $validated['slug'] = $this->resolveSlug(
            $validated['slug'] ?? '',
            $validated['title'] ?? '',
            $news
        );
        $validated['content'] = $this->sanitizeHtml($validated['content'] ?? '');
        $validated['tags'] = $this->normalizeTags($request->input('tags'));
        $validated['published_at'] = $this->resolvePublishedAt(
            $validated['status'] ?? 'draft',
            $news->published_at?->toDateTimeString()
        );

        if ($request->hasFile('cover_image')) {
            $this->deleteCoverImageIfLocal($news->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('news', 'public');
        }

        $news->update($validated);

        return redirect()
            ->route('news.index')
            ->with('success', 'Article updated.');
    }

    public function destroy(News $news)
    {
        $this->deleteCoverImageIfLocal($news->cover_image);
        $news->delete();

        return redirect()
            ->route('news.index')
            ->with('success', 'Article deleted.');
    }

    private function storeRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news,slug'],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'cover_image' => ['required', 'image', 'max:2048'],
            'category' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ];
    }

    private function updateRules(News $news): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($news->id)],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'category' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ];
    }

    private function resolveSlug(string $slug, string $title, ?News $news = null): string
    {
        $base = $slug !== '' ? $slug : $title;
        $base = Str::slug($base);
        $base = $base !== '' ? $base : Str::slug(Str::random(8));

        $candidate = $base;
        $counter = 2;

        while (News::where('slug', $candidate)
            ->when($news, fn ($query) => $query->where('id', '!=', $news->id))
            ->exists()) {
            $candidate = $base.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

    private function resolvePublishedAt(string $status, ?string $current = null): ?string
    {
        if ($status !== 'published') {
            return null;
        }

        return $current ?? now()->toDateTimeString();
    }

    private function normalizeTags($tags): ?array
    {
        if ($tags === null || $tags === '') {
            return null;
        }

        if (is_array($tags)) {
            return array_values(array_filter(array_map('trim', $tags)));
        }

        $parts = array_map('trim', explode(',', (string) $tags));
        $parts = array_values(array_filter($parts, fn ($value) => $value !== ''));

        return $parts ?: null;
    }

    private function sanitizeHtml(string $html): string
    {
        $html = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/is', '', $html);
        $html = preg_replace('/<\s*style[^>]*>.*?<\s*\/\s*style\s*>/is', '', $html);
        $html = strip_tags($html, '<p><br><strong><em><u><ol><ul><li><blockquote><h1><h2><h3><h4><h5><h6><span><div><a><img><pre><code>');
        $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html);
        $html = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $html);
        $html = preg_replace('/(href|src)\s*=\s*"\s*javascript:[^"]*"/i', '$1="#"', $html);
        $html = preg_replace("/(href|src)\s*=\s*'\s*javascript:[^']*'/i", '$1="#"', $html);

        return $html ?? '';
    }

    private function deleteCoverImageIfLocal(?string $image): void
    {
        if (!$image) {
            return;
        }

        if (Str::startsWith($image, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk('public')->delete($image);
    }
}
