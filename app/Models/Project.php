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
        'is_active',
    ];

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->withTimestamps();
    }
}
