<?php

namespace App\Domains\Subscription\Models;

use App\Domains\Module\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'billing_cycle', 'max_users'];

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'plan_module');
    }
}
