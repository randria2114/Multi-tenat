<?php

namespace App\Interfaces\Http\Controllers\Central;

use App\Application\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        protected ApiResponseService $responseService
    ) {
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseService->validationError($validator->errors());
        }

        $credentials = $request->only('email', 'password');
        // dd($credentials);
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->responseService->error('Identifiants invalides.', 401);
        }

        return $this->responseService->success([
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 'Connexion réussie.');
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return $this->responseService->success(null, 'Déconnexion réussie.');
    }

    public function me(): JsonResponse
    {
        return $this->responseService->success(auth('api')->user());
    }
}
