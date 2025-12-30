<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'supplier_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'owner_id');
    }

    // Multitenancy: Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Multitenancy: Get user's tenants (suppliers)
    public function getTenants(Panel $panel): Collection
    {
        return $this->companies()->with('traders')->get()->pluck('traders')->flatten();
    }

    // Multitenancy: Get tenant for user
    public function canAccessTenant(Model $tenant): bool
    {
        return $tenant->company->owner_id === $this->id;
    }

    // Panel access control
    //    public function canAccessPanel(Panel $panel): bool
    //    {
    //        return match ($panel->getId()) {
    //            'admin' => $this->isAdmin(),
    //            'vendor' => $this->isVendor(),
    //            'app' => $this->isUser(),
    //            default => false,
    //        };
    //    }

    // Role helper methods
    //    public function isAdmin(): bool
    //    {
    //        return $this->role === 'admin';
    //    }
    //
    //    public function isVendor(): bool
    //    {
    //        return $this->role === 'vendor';
    //    }
    //
    //    public function isUser(): bool
    //    {
    //        return $this->role === 'user' || $this->role === null;
    //    }
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
