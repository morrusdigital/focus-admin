<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectImage extends Model
{
    use HasFactory;

    protected $appends = [
        'image_url',
    ];

    protected $fillable = [
        'project_id',
        'image',
        'sort_order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getImageUrlAttribute(): string
    {
        $image = (string) $this->image;

        if ($image === '') {
            return '';
        }

        if (Str::startsWith($image, ['http://', 'https://', '/'])) {
            return $image;
        }

        return Storage::url($image);
    }
}
