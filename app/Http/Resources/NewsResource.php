<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'cover_image' => $this->cover_image,
            'cover_image_url' => $this->cover_image_url,
            'category' => $this->category,
            'author' => $this->author,
            'tags' => $this->tags,
            'status' => $this->status,
            'published_at' => optional($this->published_at)->toISOString(),
        ];
    }
}
