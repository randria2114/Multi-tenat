<?php

use App\Interfaces\Http\Controllers\Central\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Central API is running.']);
});
