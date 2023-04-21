<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NdisServiceController;

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
    Route::post('/ndisPriceImport', [NdisServiceController::class, "index"])->name("ndis_price_guide_import")->middleware('permission');
    Route::get('/getState', [NdisServiceController::class, "getState"]);
    Route::post('/getLineItemData', [NdisServiceController::class, "getLineItemData"])->name("ndis_price_guide_view_list")->middleware('permission');
    Route::post('/getLineItemDetails', [NdisServiceController::class, "getLineItemDetails"])->name("ndis_price_guide_view_details")->middleware('permission');
    Route::post('/getLineItemRate', [NdisServiceController::class, "getLineItemRate"])->name("ndis_price_guide_view_rates")->middleware('permission');
    Route::post('/saveLineItemDetails', [NdisServiceController::class, "saveLineItemDetails"])->name("ndis_price_guide_create_details")->middleware('permission');
    Route::post('/saveLineItemRates', [NdisServiceController::class, "saveLineItemRates"])->name("ndis_price_guide_create_rates")->middleware('permission');
    Route::post('/updateAndSaveLineItemRates', [NdisServiceController::class, "saveLineItemRates"])->name("ndis_price_guide_edit_rates")->middleware('permission');
});