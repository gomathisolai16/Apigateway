<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RosterTemplateController;
use App\Http\Controllers\RosterScheduleController;


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
    Route::get('/getRosterDetails', [RosterTemplateController::class, "getRosterDetails"]);
    Route::post('/getParticipantSiteData', [RosterTemplateController::class, "getParticipantSiteData"]);
    Route::post('/getEmployeePosition', [RosterTemplateController::class, "getEmployeePosition"]);
    Route::post('/getStaffSiteDetails', [RosterTemplateController::class, "getStaffSiteDetails"]);
    Route::post('/getShiftAddressData', [RosterTemplateController::class, "getShiftAddressData"]);
    Route::post('/saveRosterTemplates', [RosterTemplateController::class, "saveRosterTemplates"]);
    Route::post('/getRosterViews', [RosterTemplateController::class, "getRosterViews"]);
    Route::post('/getRosterBasicDetails', [RosterTemplateController::class, "getRosterBasicDetails"]);
    Route::post('/saveShiftRosterDetails', [RosterTemplateController::class, "saveShiftRosterDetails"]);
    Route::post('/saveRosterStatus', [RosterTemplateController::class, "saveRosterStatus"]);
    Route::post('/searchStaffShiftRoster', [RosterTemplateController::class, "searchStaffShiftRoster"]);
    Route::post('/viewRosterShifts', [RosterTemplateController::class, "viewRosterShifts"]);
    Route::post('/getRosterFilterValues', [RosterTemplateController::class, "getRosterFilterValues"]);
    Route::post('/getRosterAttributeData', [RosterTemplateController::class, "getRosterAttributeData"]);
    Route::post('/copyShiftCalender', [RosterTemplateController::class, "copyShiftCalender"]);
    Route::post('/saveAndCopyShiftData', [RosterTemplateController::class, "saveAndCopyShiftData"]);
    Route::post('/copyAllShiftsData', [RosterTemplateController::class, "copyAllShiftsData"]);
    Route::post('/rosters/viewShift', [RosterTemplateController::class, "viewShift"]);
    Route::post('/rosters/saveShiftSupportItems', [RosterTemplateController::class, "saveShiftSupportItems"]);
    Route::post('/validateSaveAndCopy', [RosterTemplateController::class, "validateSaveAndCopy"]);
    Route::post('/rosters/updateShiftRosterDetails', [RosterTemplateController::class, "updateShiftRosterDetails"]);

    //Roster Schedule 
    Route::post('/saveRosterScheduleDetails', [RosterScheduleController::class, "saveRosterScheduleDetails"]);
    Route::post('/getActiveRosterTemplateList', [RosterScheduleController::class, "getActiveRosterTemplateList"]);
    Route::post('/getRosterScheduleDetails', [RosterScheduleController::class, "getRosterScheduleDetails"]);
    Route::post('/searchStaffShiftRosterByPosition', [RosterTemplateController::class, "searchStaffShiftRosterByPosition"]);
    Route::post('/deleteShiftById', [RosterTemplateController::class, "deleteShiftById"]);
    Route::post('/viewRosterScheduleShifts', [RosterScheduleController::class, "viewRosterScheduleShifts"]);
    Route::post('/rosters/updateShiftSupportItems', [RosterTemplateController::class, "updateShiftSupportItems"]);
    Route::post('/updateRosterScheduleDetails', [RosterScheduleController::class, "updateRosterScheduleDetails"]);
    Route::post('/rosterSchedule/viewScheduleShift', [RosterScheduleController::class, "viewScheduleShift"]);
    Route::post('/publishShiftById', [RosterScheduleController::class, "publishShiftById"]);
});
