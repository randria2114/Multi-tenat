<?php

namespace App\Interfaces\Http\Controllers\Central;

use App\Application\Constants\ErrorCodes;
use App\Application\Services\ApiResponseService;
use App\Application\Services\SubscriptionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected ApiResponseService $responseService
    ) {
    }

    public function extend(Request $request, string $tenantId): JsonResponse
    {
        $request->validate([
            'months' => 'required|integer|min:1',
        ]);

        try {
            $this->subscriptionService->extendSubscription($tenantId, $request->input('months'));
            return $this->responseService->success(null, 'Abonnement prolongé avec succès.');
        } catch (\Exception $e) {
            return $this->responseService->error('Échec de la prolongation de l\'abonnement.', 500, $e->getMessage());
        }
    }

    public function changePlan(Request $request, string $tenantId): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        try {
            $this->subscriptionService->changePlan($tenantId, $request->input('plan_id'));
            return $this->responseService->success(null, 'Plan modifié avec succès.');
        } catch (\Exception $e) {
            return $this->responseService->error('Échec du changement de plan.', 500, $e->getMessage());
        }
    }
}
