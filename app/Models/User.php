<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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

    // Multitenancy: Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Multitenancy: Get user's tenants (suppliers)
    public function getTenants(Panel $panel): Collection
    {
        if ($this->isAdmin()) {
            return Supplier::all();
        }

        if ($this->isVendor() && $this->supplier) {
            return Collection::make([$this->supplier]);
        }

        return Collection::make();
    }

    // Multitenancy: Get tenant for user
    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->supplier_id === $tenant->id;
    }

    // Panel access control
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            'vendor' => $this->isVendor(),
            'app' => $this->isUser(),
            default => false,
        };
    }

    // Role helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isUser(): bool
    {
        return $this->role === 'user' || $this->role === null;
    }
}
