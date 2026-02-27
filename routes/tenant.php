<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Interfaces\Http\Middleware\CheckModuleActive;
use App\Interfaces\Http\Middleware\CheckSubscriptionActive;

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    CheckSubscriptionActive::class,
])->prefix('api')->group(function () {
    Route::get('/', function () {
        return response()->json([
            'message' => 'This is your multi-tenant application.',
            'tenant' => tenant('id')
        ]);
    });

    // Module SMS
    Route::middleware([CheckModuleActive::class . ':sms'])->group(function () {
        Route::get('/sms/logs', [\App\Interfaces\Http\Controllers\Tenant\SmsController::class, 'index']);
        Route::post('/sms/send', [\App\Interfaces\Http\Controllers\Tenant\SmsController::class, 'send']);
    });

    // Paramètres du Tenant (Client)
    Route::prefix('settings')->group(function () {
        Route::put('/domain', [\App\Interfaces\Http\Controllers\Tenant\TenantSettingsController::class, 'updateDomain']);
        Route::put('/api-config', [\App\Interfaces\Http\Controllers\Tenant\TenantSettingsController::class, 'updateSettings']);
    });
});
