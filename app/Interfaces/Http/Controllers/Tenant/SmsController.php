<?php

namespace App\Interfaces\Http\Controllers\Tenant;

use App\Application\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SmsController extends Controller
{
    public function __construct(
        protected ApiResponseService $responseService
    ) {
    }

    public function index(): JsonResponse
    {
        // This is a placeholder for SMS logs
        // In a real app, you would fetch from the database
        $logs = [
            [
                'id' => 1,
                'to' => '+33123456789',
                'message' => 'Hello from Tenant API',
                'status' => 'sent',
                'created_at' => now()->toDateTimeString(),
            ]
        ];

        return $this->responseService->success($logs, 'Logs SMS récupérés avec succès.');
    }

    public function send(): JsonResponse
    {
        return $this->responseService->success(null, 'SMS envoyé avec succès (Simulation).');
    }
}
