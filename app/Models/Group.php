<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_groups'); // Pivot table 'group_user'
    }
}
