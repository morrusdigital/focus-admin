<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    protected $appends = [
        'logo_url',
    ];

    protected $fillable = [
        'name',
        'logo',
        'sort_order',
        'is_active',
    ];

    public function getLogoUrlAttribute(): string
    {
        $logo = (string) $this->logo;

        if ($logo === '') {
            return '';
        }

        if (Str::startsWith($logo, ['http://', 'https://', '/'])) {
            return $logo;
        }

        return Storage::url($logo);
    }
}
