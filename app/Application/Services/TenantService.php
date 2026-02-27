<?php

namespace App\Application\Services;

use App\Domains\Tenant\Models\Tenant;
use App\Domains\Subscription\Models\Plan;
use Illuminate\Support\Facades\DB;

class TenantService
{
    public function createTenant(string $id, string $domain, int $planId): Tenant
    {
        $plan = Plan::with('modules')->findOrFail($planId);

        // 1. Créer le Tenant (Client)
        $tenant = Tenant::create([
            'id' => $id,
            'plan_id' => $plan->id,
            'subscription_expires_at' => now()->addMonth(), // Par défaut 1 mois
        ]);

        // 2. Créer le Domaine associé
        $tenant->createDomain([
            'domain' => $domain,
        ]);

        // 3. Attacher les Modules associés au Plan
        foreach ($plan->modules as $module) {
            $tenant->modules()->attach($module->id, ['is_active' => true]);
        }

        return $tenant;
    }

    public function getAllTenants()
    {
        return Tenant::with('domains', 'plan.modules')->get();
    }

    public function getTenantById(string $id)
    {
        return Tenant::with('domains', 'plan.modules', 'modules')->findOrFail($id);
    }

    public function deleteTenant(string $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();
    }

    public function updateDomain(Tenant $tenant, string $newDomain): void
    {
        // Stancl Tenancy : mettre à jour l'enregistrement du domaine
        $tenant->domains()->update(['domain' => $newDomain]);
    }

    public function updateSettings(Tenant $tenant, array $settings): void
    {
        // Stocker les paramètres dans la colonne JSON 'data'
        $currentData = $tenant->data ?? [];
        $tenant->data = array_merge($currentData, $settings);
        $tenant->save();
    }
}
