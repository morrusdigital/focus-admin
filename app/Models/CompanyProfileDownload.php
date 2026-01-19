<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfileDownload extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'domicile',
        'ip_address',
        'user_agent',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];
}
