<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserType;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

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
        return $this->can_access_admin_panel && $this->user_type === UserType::ADMIN;
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can_access_admin_panel || in_array($this->email, explode(',', config('app.super_admin_emails')));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logOnly(['*']);
    }
}
