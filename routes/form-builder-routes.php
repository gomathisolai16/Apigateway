<?php

use App\Http\Controllers\FormBuilderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Form builder Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| API endpoints that require valid token to access
*/

Route::middleware('auth:api')->prefix('v1/' . config('gateway.admin_prefix'))->group(function () {
    # form builder
    Route::get('/formBuilder/getModulesList', [FormBuilderController::class, 'getModulesList']);
    Route::post('/formBuilder/getSubModulesList', [FormBuilderController::class, 'getSubModulesList']);
    Route::post('/formBuilder/getFormFields', [FormBuilderController::class, 'getFormFields']);
});
