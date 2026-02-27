<?php

namespace App\Application\Services;

use App\Domains\Tenant\Models\Tenant;
use App\Domains\Subscription\Models\Plan;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Change a tenant's plan.
     */
    public function changePlan(string $tenantId, int $newPlanId): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $plan = Plan::with('modules')->findOrFail($newPlanId);

        $tenant->update([
            'plan_id' => $plan->id,
        ]);

        // Sync modules (optional: you might want to keep custom modules)
        $tenant->modules()->sync(
            $plan->modules->pluck('id')->mapWithKeys(function ($id) {
                return [$id => ['is_active' => true]];
            })
        );
    }

    /**
     * Extend subscription.
     */
    public function extendSubscription(string $tenantId, int $months = 1): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $currentExpiry = $tenant->subscription_expires_at ?? now();

        $tenant->update([
            'subscription_expires_at' => Carbon::parse($currentExpiry)->addMonths($months),
        ]);
    }

    /**
     * Suspend subscription.
     */
    public function suspendSubscription(string $tenantId): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->update([
            'subscription_expires_at' => now()->subDay(),
        ]);
    }
}
