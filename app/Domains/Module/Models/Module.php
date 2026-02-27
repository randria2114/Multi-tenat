<?php

namespace App\Domains\Module\Models;

use App\Domains\Subscription\Models\Plan;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_module');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot('is_active')
            ->withTimestamps();
    }
}
