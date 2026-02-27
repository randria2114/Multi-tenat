<?php

namespace App\Application\Services;

use App\Domains\Subscription\Models\Plan;
use Illuminate\Database\Eloquent\Collection;

class PlanService
{
    public function getAllPlans(): Collection
    {
        return Plan::with('modules')->get();
    }

    public function createPlan(array $data): Plan
    {
        $plan = Plan::create($data);
        if (isset($data['module_ids'])) {
            $plan->modules()->sync($data['module_ids']);
        }
        return $plan;
    }

    public function updatePlan(int $id, array $data): Plan
    {
        $plan = Plan::findOrFail($id);
        $plan->update($data);
        if (isset($data['module_ids'])) {
            $plan->modules()->sync($data['module_ids']);
        }
        return $plan;
    }

    public function deletePlan(int $id): void
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
    }
}
