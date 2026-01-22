<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoVideo extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'name',
        'link',
        'appear',
        'rank',
    ];

    protected $casts = [
        'appear' => 'integer',
    ];
}
