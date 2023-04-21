<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffImportController;
use App\Http\Controllers\StaffServiceController;


/*

|--------------------------------------------------------------------------
| API Routes - NDIS Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| API endpoints that require valid token to access
*/
Route::middleware('auth:api')->prefix('v1/'.config('gateway.admin_prefix'))->group(function () {
    Route::post('/staffImport', [StaffImportController::class, "store"]);
    Route::post('/staff/getStaffPayrollDetails', [StaffServiceController::class, "getStaffPayrollDetails"]);
    Route::post('/staff/saveChanges', [StaffServiceController::class, "saveChanges"]);
    Route::post('/staff/imageUpload', [StaffServiceController::class, "imageUpload"]);
});