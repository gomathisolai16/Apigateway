<?php

use App\Http\Controllers\CheckServiceController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\Oauth\AccessTokenController;
use App\Http\Controllers\ProgressNoteTemplateController;
use App\Http\Controllers\PublicHolidayController;
use App\Http\Controllers\RoleServiceController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StaffImportController;
use App\Http\Controllers\StaffServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->prefix('v1')->group(function () {
    //Route for checking if the user is logged in
    Route::get('/auth-check', function (Request $request) {
        return "User Authenticated";
    });

    Route::post('logout', [AccessTokenController::class, 'removeToken']);
    Route::post('changepassword', [AccessTokenController::class, 'changePassword']); //ChangePassword
});

Route::prefix('login')->group(function () {
    Route::post('auth', [AccessTokenController::class, 'issueToken']);
    Route::post('refresh', [AccessTokenController::class, 'refreshToken']);
    Route::post('resendotp', [AccessTokenController::class, 'resendOtp']);
    Route::post('verifyotp', [AccessTokenController::class, 'verifyOtp']);
    Route::post('resetotp', [AccessTokenController::class, 'resetOtp']);
    Route::post('getOtpCreatedAt', [AccessTokenController::class, 'getOtpGeneratedAt']);
});

Route::post('/account/activation', [AccessTokenController::class, 'accountActivation'])->name('account.activation');
Route::post('/password/set', [AccessTokenController::class, 'setPassword'])->name('password.set');
Route::post('/password/checkreset', [AccessTokenController::class, 'checkReset'])->name('password.checkReset');
Route::post('/userPermission', [AccessTokenController::class, 'userPermission'])->name('userPermission');
//Password reset route using from laravel UI

Auth::routes();

Route::middleware('auth:api')->prefix('v1/' . config('gateway.admin_prefix'))->group(function () {
    Route::post('/staffloadimport', [StaffImportController::class, "store"]);
    Route::post('/getDetailsData', [StaffServiceController::class, "getStaffDetailsData"])->name("staff_view_list")->middleware('permission');
    Route::post('/getstaffItemData', [StaffServiceController::class, "getstaffItemData"]);
    Route::post('/getStaffDetails', [StaffServiceController::class, "getStaffDetails"]);
    Route::get('/getProgramData', [StaffServiceController::class, "getProgramData"]);
    Route::post('/getStaffByProgram', [StaffServiceController::class, "getStaffByProgram"]);
    Route::post('/getSiteData', [StaffServiceController::class, "getSiteData"]);
    Route::post('/getStaffByStatus', [StaffServiceController::class, "getStaffByStatus"]);
    Route::get('/getPositionData', [StaffServiceController::class, "getPositionData"]);
    Route::get('/getAttributeData', [StaffServiceController::class, "getAttributeData"]);
    Route::post('/getManagerData', [StaffServiceController::class, "getManagerData"]);
    Route::post('/getProfileManagerData', [StaffServiceController::class, "getProfileManagerData"]);
    Route::post('/staff/saveprofileinfomation', [StaffServiceController::class, "saveProfileInfomation"])->name("staff_create_basic_details")->middleware('permission');
    Route::post('/staff/saveemploymentinfomation', [StaffServiceController::class, "saveEmploymentInfomation"])->name("staff_create_employment_details")->middleware('permission');
    Route::post('/staff/savequalificationinfomation', [StaffServiceController::class, "saveQualificationInfomation"])->name("staff_create_license_and_qualification_details")->middleware('permission');
    Route::post('/staff/saveothersinfomation', [StaffServiceController::class, "saveOthersInfomation"])->name("staff_create_others_details")->middleware('permission');
    Route::post('/staff/getStaffProfileDetails', [StaffServiceController::class, "getStaffProfileDetails"])->name("staff_view")->middleware('permission');
    Route::get('/staff/getPayrollCat', [StaffServiceController::class, "getPayrollCategory"]);
    Route::post('/staff/getPayrollLevel', [StaffServiceController::class, "getPayrollLevel"]);
    Route::post('/staff/getPayrollPoint', [StaffServiceController::class, "getPayrollPayPoint"]);
    Route::post('/staff/saveStaffPayrollDetails', [StaffServiceController::class, "saveStaffPayrollDetails"])->name("staff_create_payroll_details")->middleware('permission');
    Route::post('/staff/updateStaffStatus', [StaffServiceController::class, "updateStaffStatus"]);
    Route::post('/saveProfileManagerAndStatus', [StaffServiceController::class, "saveProfileManagerAndStatus"]);

    //My Profile
    Route::post('/staff/getMyProfileDetails', [StaffServiceController::class, "getStaffProfileDetails"]);
    Route::post('/staff/savemyprofileinfomation', [StaffServiceController::class, "saveProfileInfomation"]);
    Route::post('/staff/savemyemploymentinfomation', [StaffServiceController::class, "saveEmploymentInfomation"]);
    Route::post('/staff/savemyqualificationinfomation', [StaffServiceController::class, "saveQualificationInfomation"]);
    Route::post('/staff/savemyothersinfomation', [StaffServiceController::class, "saveOthersInfomation"]);

    // Loggedin user detail
    Route::post('/getLoggedinUser', [StaffServiceController::class, "getLoggedinUser"]);

    // Common filter
    Route::post('/getFilterParam', [FilterController::class, "getFilterParam"]);
    Route::post('/getFilterResult', [FilterController::class, "getFilterResult"]);
    Route::get('/getRoles', [RoleServiceController::class, "getRolesData"]); //Roles
    Route::get('/getEditRoles', [RoleServiceController::class, "getEditRolesData"]); //Roles
    Route::get('/getRoleList', [RoleServiceController::class, "getAllRoleList"]); //Roles
    Route::get('/getModules', [RoleServiceController::class, "getModulesData"]); //Modules
    Route::post('/saveModules', [RoleServiceController::class, "saveRoleBasedModules"]); //Modules
    Route::post('/saveUserModules', [RoleServiceController::class, "saveUserBasedModules"]); //Modules
    Route::post('/getPermissions', [RoleServiceController::class, "getPermissionData"]); //Modules
    Route::post('/saveRolePermission', [RoleServiceController::class, "saveRolePermission"]); //Modules
    Route::post('/saveUserPermission', [RoleServiceController::class, "saveUserPermission"]); //Modules
    Route::post('/getRolePermission', [RoleServiceController::class, "getRolePermission"]); //Modules
    Route::post('/getModuleBasedRole', [RoleServiceController::class, "getModuleBasedRole"]); //Modules
    Route::post('/saveRoleStatus', [RoleServiceController::class, "saveRoleStatus"]); //Role status
    Route::post('/savePortalAccess', [RoleServiceController::class, "savePortalAccess"]); //Manage Access status
    Route::post('/getRoleWithStaffStatus', [RoleServiceController::class, "getRoleWithStaffStatus"]);
    Route::post('/getStaffByPortalAccess', [RoleServiceController::class, "getStaffByPortalAccess"]);//Manage Access restrict disable portal access

    // Check service
    Route::post('/checkService', [CheckServiceController::class, "checkService"]);
    Route::post('/notificationList', [CommunicationController::class, 'notificationList']);
    Route::post('/notificationDetail', [CommunicationController::class, 'notificationDetail']);

    // Sites
    Route::get('/siteList', [SiteController::class, 'siteList'])->name("sites_list_view_list")->middleware('permission');
    Route::post('/saveSite', [SiteController::class, 'saveSite'])->name("sites_list_create_basic_details")->middleware('permission');
    Route::post('/saveSiteCoordinator', [SiteController::class, 'saveSiteCoordinator'])->name("sites_list_create_coordinator_details")->middleware('permission');
    Route::post('/viewSite', [SiteController::class, 'viewSite'])->name("sites_list_view_basic_details")->middleware('permission');
    Route::post('/getCoordinatorList', [SiteController::class, 'getCoordinatorList']);
    Route::post('/site/siteSaveChanges', [SiteController::class, "siteSaveChanges"]);
    Route::post('/site/checkSiteDetails', [SiteController::class, "checkSiteDetails"]);
    Route::post('/historyDetails', [CommunicationController::class, 'historyDetails']); //history details service

    //public holiday
    Route::post('/publicHolidayList', [PublicHolidayController::class, 'publicHolidayList'])->name("public_holiday_view_list")->middleware('permission');
    Route::post('/savePublicHoliday', [PublicHolidayController::class, 'savePublicHoliday'])->name("public_holiday_create_details")->middleware('permission');
    Route::post('/viewPublicHoliday', [PublicHolidayController::class, 'viewPublicHoliday'])->name("public_holiday_view_details")->middleware('permission');
    Route::post('/savePublicHolidayStatus', [PublicHolidayController::class, "savePublicHolidayStatus"]); //Role status

    // Leave
    Route::post('/getLeaveListData', [LeaveController::class, "getLeaveListData"]);
    Route::post('/getStaffNameListData', [LeaveController::class, "getStaffNameListData"]);
    Route::post('/saveLeaveDetails', [LeaveController::class, "saveLeaveDetails"]);
    Route::post('/getRosterDetails', [LeaveController::class, "getRosterDetails"]);
    Route::post('/getLeaveDetailsData', [LeaveController::class, "getLeaveDetailsData"]);
    Route::post('/getReportingStaffManagerData', [LeaveController::class, "getReportingStaffManagerData"]);
    Route::post('/getLeaveBalances', [LeaveController::class, "getLeaveBalances"]);
    Route::post('/deleteLeaveDetails', [LeaveController::class, "deleteLeaveDetails"]);
    Route::post('/getRequestLeaveListData', [LeaveController::class, "getRequestLeaveListData"]);
    Route::post('/leaveApplicationConfirmation', [LeaveController::class, "leaveApplicationConfirmation"]);
    Route::post('/saveLeaveDocument', [LeaveController::class, "saveLeaveDocument"]);

    Route::post('/participantHistoryDetails', [CommunicationController::class, 'participantHistoryDetails']);

    //master data
    Route::get('/getMasterListData', [MasterDataController::class, 'getMasterListData']);
    Route::get('/getMasterDataType', [MasterDataController::class, 'getMasterDataType']);
    Route::post('/saveMasterData', [MasterDataController::class, 'saveMasterData']);
    Route::post('/archiveMasterData', [MasterDataController::class, 'archiveMasterData']);
    Route::post('/updateMasterData', [MasterDataController::class, 'updateMasterData']);

    // Progress Note template
    Route::get('/progressTemplateList', [ProgressNoteTemplateController::class, 'progressTemplateList']);
    Route::get('/getProgramOptions', [ProgressNoteTemplateController::class, 'getProgramOptions']);
    Route::post('/saveProgressNoteTemplate', [ProgressNoteTemplateController::class, 'saveProgressNoteTemplate']);
    Route::post('/saveProgressQuestionDetails', [ProgressNoteTemplateController::class, 'saveProgressQuestionDetails']);
    Route::post('/getProgressTemplateDetails', [ProgressNoteTemplateController::class, 'getProgressTemplateDetails']);
    Route::post('/saveProgressTemplateStatus', [ProgressNoteTemplateController::class, "saveProgressTemplateStatus"]);
    Route::post('/getQuestionOrderList', [ProgressNoteTemplateController::class, "getQuestionOrderList"]);

});
