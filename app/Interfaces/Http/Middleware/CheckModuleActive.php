<?php

namespace App\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        $tenant = tenant();

        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 403);
        }

        $isModuleActive = $tenant->modules()
            ->where('slug', $moduleSlug)
            ->wherePivot('is_active', true)
            ->exists();

        if (!$isModuleActive) {
            return response()->json([
                'message' => "The module '{$moduleSlug}' is not active for your account."
            ], 403);
        }

        return $next($request);
    }
}
