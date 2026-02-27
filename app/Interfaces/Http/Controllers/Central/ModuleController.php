<?php

namespace App\Interfaces\Http\Controllers\Central;

use App\Application\Services\ApiResponseService;
use App\Application\Services\ModuleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    public function __construct(
        protected ModuleService $moduleService,
        protected ApiResponseService $responseService
    ) {
    }

    public function index(): JsonResponse
    {
        return $this->responseService->success($this->moduleService->getAllModules());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:modules,name',
            'slug' => 'required|string|unique:modules,slug',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        $module = $this->moduleService->createModule($request->all());

        return $this->responseService->created($module, 'Module créé avec succès.');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|unique:modules,name,' . $id,
            'slug' => 'string|unique:modules,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        $module = $this->moduleService->updateModule($id, $request->all());

        return $this->responseService->success($module, 'Module mis à jour avec succès.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->moduleService->deleteModule($id);
        return $this->responseService->success(null, 'Module supprimé avec succès.');
    }
}
