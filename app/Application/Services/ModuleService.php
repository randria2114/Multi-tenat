<?php

namespace App\Application\Services;

use App\Domains\Module\Models\Module;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

class ModuleService
{
    public function getAllModules(): Collection
    {
        return Module::all();
    }

    public function createModule(array $data): Module
    {
        return Module::create($data);
    }

    public function updateModule(int $id, array $data): Module
    {
        $module = Module::findOrFail($id);
        $module->update($data);
        return $module;
    }

    public function deleteModule(int $id): void
    {
        $module = Module::findOrFail($id);
        $module->delete();
    }

    /**
     * Activate a module for a tenant.
     */
    public function activateModule(string $tenantId, int $moduleId): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->modules()->updateExistingPivot($moduleId, ['is_active' => true]);
    }

    /**
     * Deactivate a module for a tenant.
     */
    public function deactivateModule(string $tenantId, int $moduleId): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->modules()->updateExistingPivot($moduleId, ['is_active' => false]);
    }
}
