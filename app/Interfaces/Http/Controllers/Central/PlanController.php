<?php

namespace App\Interfaces\Http\Controllers\Central;

use App\Application\Services\ApiResponseService;
use App\Application\Services\PlanService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $planService,
        protected ApiResponseService $responseService
    ) {
    }

    public function index(): JsonResponse
    {
        return $this->responseService->success($this->planService->getAllPlans());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|string|in:monthly,yearly',
            'max_users' => 'required|integer',
            'module_ids' => 'array|exists:modules,id',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        $plan = $this->planService->createPlan($request->all());

        return $this->responseService->created($plan->load('modules'), 'Plan créé avec succès.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'price' => 'numeric',
            'billing_cycle' => 'string|in:monthly,yearly',
            'max_users' => 'integer',
            'module_ids' => 'array|exists:modules,id',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        $plan = $this->planService->updatePlan($id, $request->all());

        return $this->responseService->success($plan->load('modules'), 'Plan mis à jour avec succès.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->planService->deletePlan($id);
        return $this->responseService->success(null, 'Plan supprimé avec succès.');
    }
}
