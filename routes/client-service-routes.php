<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use Laravel\Passport\Console\ClientCommand;

/*

|--------------------------------------------------------------------------
| API Routes - Client Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| API endpoints that require valid token to access
*/

Route::middleware('auth:api')->prefix('v1/' . config('gateway.admin_prefix'))->group(function () {
    Route::get('/culturalIdentityList', [ClientController::class, "culturalIdentityList"]);
    Route::get('/decisionMakerList', [ClientController::class, "decisionMakerList"]);
    Route::post('/getStaffOrSite', [ClientController::class, "getStaffOrSite"]);
    Route::post('/saveParticipant', [ClientController::class, "saveParticipant"])->name("participants_create_basic_details")->middleware('permission');
    Route::post('/listParticipants', [ClientController::class, 'listParticipants'])->name("participants_view_list")->middleware('permission');
    Route::post('/viewParticipant', [ClientController::class, 'viewParticipant'])->name("participants_view_basic_details")->middleware('permission');
    Route::get('/getProgramList', [ClientController::class, "getProgramList"]);
    Route::post('/participants/saveChanges', [ClientController::class, "saveChanges"]);
    Route::post('/importParticipants', [ClientController::class, 'importParticipants']);

    # service agreement
    Route::get('/serviceAgreement/planManageTypes', [ClientController::class, 'planManageTypes']);
    Route::post('/serviceAgreement/savePlanDetails', [ClientController::class, 'savePlanDetails']);
    Route::post('/serviceAgreement/listPlanDetails', [ClientController::class, 'listPlanDetails']);
    Route::post('/serviceAgreement/breadcrumbs', [ClientController::class, 'breadcrumbs']);
    Route::post('/serviceAgreement/viewPlanDetail', [ClientController::class, 'viewPlanDetail']);
    Route::post('/serviceAgreement/clonePlanDetail', [ClientController::class, 'clonePlanDetail']);
    Route::post('/serviceAgreement/updateSAStatus', [ClientController::class, 'updateSAStatus']);

    #service agreement - SOS
    Route::post('/serviceAgreement/getSupportCategory', [ClientController::class, 'getSupportCategory']);
    Route::post('/serviceAgreement/getLineItem', [ClientController::class, 'getLineItem']);
    Route::post('/serviceAgreement/saveScheduleOfSupport', [ClientController::class, 'saveScheduleOfSupport']);
    Route::post('/serviceAgreement/listScheduleOfSupport', [ClientController::class, 'listScheduleOfSupport']);
    Route::post('/serviceAgreement/finalize', [ClientController::class, 'finalize']);
    Route::get('/serviceAgreement/cronJobStatusArchive', [ClientController::class, 'cronJobStatusArchive']);

    # service agreement - documents
    Route::get('/serviceAgreement/getDocumentTemplate', [ClientController::class, 'getDocumentTemplate']);
    Route::post('/serviceAgreement/saveDocument', [ClientController::class, 'saveDocument']);
    Route::post('/serviceAgreement/listDocuments', [ClientController::class, 'listDocuments']);
    Route::post('/serviceAgreement/updateSAStatus', [ClientController::class, 'updateSAStatus']);
    Route::post('/serviceAgreement/uploadSignedDocument', [ClientController::class, 'uploadSignedDocument']);
    
    # service agreement - service booking
    Route::post('/serviceAgreement/saveServiceBooking', [ClientController::class, 'saveServiceBooking']);
    Route::post('/serviceAgreement/getServiceBooking', [ClientController::class, 'getServiceBooking']);
});
