<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectImage;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sector',
        'location',
        'status',
        'description',
        'badge',
        'sort_order',
        'is_active',
    ];

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }
}
