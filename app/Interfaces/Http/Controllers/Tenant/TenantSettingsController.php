<?php

namespace App\Interfaces\Http\Controllers\Tenant;

use App\Application\Services\ApiResponseService;
use App\Application\Services\TenantService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantSettingsController extends Controller
{
    public function __construct(
        protected TenantService $tenantService,
        protected ApiResponseService $responseService
    ) {
    }

    public function updateDomain(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:255|unique:mysql.domains,domain',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        try {
            $tenant = tenant();
            $this->tenantService->updateDomain($tenant, $request->input('domain'));

            return $this->responseService->success(null, 'Domaine mis à jour avec succès.');
        } catch (\Exception $e) {
            return $this->responseService->error('Échec de la mise à jour du domaine.', 500, $e->getMessage());
        }
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        try {
            $tenant = tenant();
            $this->tenantService->updateSettings($tenant, $request->input('settings'));

            return $this->responseService->success(null, 'Paramètres mis à jour avec succès.');
        } catch (\Exception $e) {
            return $this->responseService->error('Échec de la mise à jour des paramètres.', 500, $e->getMessage());
        }
    }
}
