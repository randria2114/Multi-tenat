<?php

use App\Interfaces\Http\Controllers\Central\TenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login', [\App\Interfaces\Http\Controllers\Central\AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [\App\Interfaces\Http\Controllers\Central\AuthController::class, 'logout']);
    Route::get('/auth/me', [\App\Interfaces\Http\Controllers\Central\AuthController::class, 'me']);

    // Tenants Management
    Route::apiResource('tenants', TenantController::class);
    Route::post('/tenants/{tenant}/extend', [\App\Interfaces\Http\Controllers\Central\SubscriptionController::class, 'extend']);
    Route::post('/tenants/{tenant}/plan', [\App\Interfaces\Http\Controllers\Central\SubscriptionController::class, 'changePlan']);

    // Modules CRUD
    Route::apiResource('modules', \App\Interfaces\Http\Controllers\Central\ModuleController::class);

    // Plans CRUD
    Route::apiResource('plans', \App\Interfaces\Http\Controllers\Central\PlanController::class);
});