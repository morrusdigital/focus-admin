<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectRequest extends Model
{
    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'project_location',
        'area_estimate',
        'timeline',
        'project_description',
        'project_images',
        'status',
        'notes',
    ];

    protected $casts = [
        'project_images' => 'array',
    ];

    public function getProjectImagesUrlsAttribute(): array
    {
        $images = $this->project_images ?? [];

        // dd($images);

        return array_values(array_filter(array_map(function ($image) {
            if (!$image) {
                return null;
            }

            if (Str::startsWith($image, ['http://', 'https://', '/'])) {
                return $image;
            }

            return Storage::disk('public')->url($image);
        }, $images)));
    }
}
