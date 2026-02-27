<?php

namespace App\Interfaces\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if (!$tenant) {
            return response()->json(['message' => 'Tenant context not found.'], 403);
        }

        if ($tenant->subscription_expires_at && $tenant->subscription_expires_at->isPast()) {
            return response()->json([
                'message' => 'Your subscription has expired. Please renew to continue.'
            ], 402);
        }

        return $next($request);
    }
}
