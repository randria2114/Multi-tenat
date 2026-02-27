<?php

namespace App\Domains\Tenant\Models;

use App\Domains\Module\Models\Module;
use App\Domains\Subscription\Models\Plan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $casts = [
        'subscription_expires_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'plan_id',
            'subscription_expires_at',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules', 'tenant_id', 'module_id')
            ->withPivot('is_active') // Indique si le module est actif pour ce client
            ->withTimestamps();
    }
}
