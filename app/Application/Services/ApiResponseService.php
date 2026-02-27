<?php

namespace App\Application\Services;

use Illuminate\Http\JsonResponse;

class ApiResponseService
{
    /**
     * Return a success JSON response.
     */
    public function success(mixed $data = null, string $message = 'Succès', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    public function error(string $message = 'Erreur', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Return a resource created JSON response.
     */
    public function created(mixed $data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Return a validation error response.
     */
    public function validationError(mixed $errors): JsonResponse
    {
        return $this->error('La validation a échoué', 422, $errors);
    }
}
