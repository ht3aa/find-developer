<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'status',
    ];

    protected $casts = [
        'status' => CompanyStatus::class,
    ];

    #[Scope]
    public function active(Builder $query): Builder
    {
        return $query->where('status', CompanyStatus::ACTIVE);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Company $company): void {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
        });
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'company_skill')
            ->withTimestamps();
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            return $this->logo_path
                ? StorageHelper::url($this->logo_path)
                : null;
        });
    }
}
