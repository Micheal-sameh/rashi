<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use Auditable, HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'membership_code',
        'phone',
        'score',
        'points',
        'email_verified_at',
        'password',
        'image',
    ];

    protected $mediaAttributes = [
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_groups');
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }
}
