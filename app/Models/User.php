<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserType;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'can_access_admin_panel',
        'user_type',
        'linkedin_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_access_admin_panel' => 'boolean',
            'user_type' => UserType::class,
        ];
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->email, explode(',', config('app.super_admin_emails')));
    }

    public function isDeveloper(): bool
    {
        return $this->user_type === UserType::DEVELOPER && $this->developer()->exists();
    }

    public function isAdmin(): bool
    {
        return $this->user_type === UserType::ADMIN;
    }

    public function isHR(): bool
    {
        return $this->user_type === UserType::HR;
    }

    public function developer(): HasOne
    {
        return $this->hasOne(Developer::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(UserService::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(UserAppointment::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
