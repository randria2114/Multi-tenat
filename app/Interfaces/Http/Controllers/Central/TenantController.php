<?php

namespace App\Interfaces\Http\Controllers\Central;

use App\Application\Constants\ErrorCodes;
use App\Application\Services\ApiResponseService;
use App\Application\Services\TenantService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    public function __construct(
        protected TenantService $tenantService,
        protected ApiResponseService $responseService
    ) {
    }

    public function index(): JsonResponse
    {
        return $this->responseService->success($this->tenantService->getAllTenants());
    }

    public function show(string $id): JsonResponse
    {
        try {
            return $this->responseService->success($this->tenantService->getTenantById($id));
        } catch (\Exception $e) {
            return $this->responseService->error('Tenant non trouvé.', 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|unique:tenants,id|max:255',
            'subdomain' => 'required|string|max:255',
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        $domain = $request->input('subdomain') . '.' . config('app.url_base', 'localhost');

        try {
            $tenant = $this->tenantService->createTenant(
                $request->input('id'),
                $domain,
                $request->input('plan_id')
            );

            return $this->responseService->created([
                'id' => $tenant->id,
                'domain' => $domain,
            ], 'Client créé avec succès.');
        } catch (\Exception $e) {
            return $this->responseService->error(
                ErrorCodes::getMessage(ErrorCodes::TENANT_CREATION_FAILED),
                500,
                $e->getMessage()
            );
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'sometimes|exists:plans,id',
            'subdomain' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        try {
            $tenant = Tenant::findOrFail($id);
            
            if ($request->has('subdomain')) {
                $domain = $request->input('subdomain') . '.' . config('app.url_base', 'localhost');
                $this->tenantService->updateDomain($tenant, $domain);
            }

            if ($request->has('plan_id')) {
                // You might need a more complex logic here if plan change involves module updates
                $tenant->update(['plan_id' => $request->input('plan_id')]);
            }

            return $this->responseService->success($tenant->load('domains'), 'Tenant mis à jour avec succès.');
        } catch (\Exception $e) {
            return $this->responseService->error('Échec de la mise à jour.', 500, $e->getMessage());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->tenantService->deleteTenant($id);
            return $this->responseService->success(null, 'Tenant supprimé avec succès.');
        } catch (\Exception $e) {
            return $this->responseService->error('Échec de la suppression.', 500, $e->getMessage());
        }
    }
}
