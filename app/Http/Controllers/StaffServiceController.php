<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\User;
use App\Services\HistoryService;
use App\Services\StaffService;
use App\Traits\AccountActivations;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StaffServiceController extends Controller
{
    use ApiResponser;
    use AccountActivations;
    /**
     * The service to consume the admin microservice
     * @var PostService
     */

    /**
     * This method requests and returns all posts of a user from post microservice
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */

    public function index($collection)
    {
        $staffService = new StaffService;

        return $this->response($staffService->index($collection));
    }

    /**
     * @lrd:start
     *  To Get staff data  details by uuid
     * @lrd:end
     *
     * @QAparam uuid string required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function getStaffDetailsData(Request $request)
    {
        $staffService = new StaffService;
        $response = (array) json_decode($staffService->getStaffDetailsData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * @lrd:start
     *  To Get program option list
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProgramData(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getProgramData($request);
    }

    /**
     * @lrd:start
     *  Check staff is Manager/Coordinator to any active staff
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStaffByProgram(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getStaffByProgram($request);
    }

    /**
     * @lrd:start
     *  Check staff is Manager/Coordinator to any active staff based on status
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStaffByStatus(Request $request)
    {
        $staffService = new StaffService;
        $response = $staffService->getStaffByStatus($request);
        return $response;
    }
    /**
     * @lrd:start
     *  To Get program option list
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSiteData(Request $request)
    {
        $staffService = new StaffService;
        $response = (array) json_decode($staffService->getSiteData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }
    /**
     * @lrd:start
     *  To Get position option list
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPositionData(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getPositionData($request);
    }

    /**
     * @lrd:start
     *  To Get attributes
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttributeData(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getAttributeData($request);
    }

    /**
     * @lrd:start
     *  To Get manager option list
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getManagerData(Request $request)
    {
        $params = $request->all();
        $user_id = $params['user_id'];
        $user = [];
        $userIdList = [];
        $userImport = new UsersImport;
        $response = (array) $userImport->getManagerData($request);
        if (!empty($response) && $response['status'] === true) {
            $manager = $response['data']->manager;
            $siteCoordinator = $response['data']->siteCoordinator;
            if (!empty($manager)) {
                $user = User::getManagerbyManagerIDs($user_id, $manager);
                $userIdList = User::getManagerIDbyManagerIDs($user_id, $manager);
            }
        }
        $data['manager'] = $user;
        $data['manager_id'] = $userIdList;
        $data['siteCoordinator'] = $siteCoordinator;
        $msg = "";
        $code = Response::HTTP_OK;
        return $this->response($msg, $code, $data);
    }

    /**
     * @lrd:start
     *  To Get profile manager option list
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfileManagerData(Request $request)
    {
        $params = $request->all();
        $user_id = $params['user_id'];
        $user = [];
        $userIdList = [];
        $userImport = new UsersImport;
        $response = (array) $userImport->getManagerData($request);
        if (!empty($response) && $response['status'] === true) {
            $manager = $response['data']->manager;
            if (!empty($manager)) {
                $user = User::getManagerbyManagerIDs($user_id, $manager);
                $userIdList = User::getManagerIDbyManagerIDs($user_id, $manager);
            }
        }
        $data['manager'] = $user;
        $data['manager_id'] = $userIdList;
        $msg = "";
        $code = Response::HTTP_OK;
        return $this->response($msg, $code, $data);
    }

    public function getStaffDetails(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getStaffDetails($request);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add staff basic profile data insert
     * @lrd:end
     *
     * @QAparam first_name string required Smith
     * @QAparam last_name string required jhon
     * @QAparam program string required Day Program
     * @QAparam primary_site string required Bingara
     * @QAparam date_of_birth date required 17-07-1993
     * @QAparam gender string required Male
     * @QAparam residency_status string required Australian
     * @QAparam status string required Active/Inactive
     * @QAparam portal_access string required Yes or No
     * @QAparam primary_contact_number number required 10digit
     * @QAparam primary_email string required email|unique:users
     * @QAparam primary_address string required Australian
     * @QAparam is_postal_address_different string required
     * @QAparam emergency_contact_name string required Smith
     * @QAparam emergency_contact_number number required 10digit
     * @QAparam relationship string required Yes
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveProfileInfomation(Request $request)
    {

        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "first_name" => "required",
                "last_name" => "required",
                "program" => "required",
                "primary_site" => "required",
                "date_of_birth" => "required",
                "gender" => "required",
                "residency_status" => "required",
                "portal_access" => "required",
                "primary_contact_number" => "required",
                "primary_email" => "required",
                "primary_address" => "required",
                "is_postal_address_different" => "required",
                "emergency_contact_name" => "required",
                "emergency_contact_number" => "required",
                "relationship" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => implode(',', $validator->errors()->all()),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $userImport = new UsersImport;
            $response = (array) $userImport->saveProfileInfomation($request);

            # Display toasted for custom validation message
            if (!empty($response['cutom_validation_message'])) {
                return $this->response(
                    $response['cutom_validation_message'],
                    $response['code'],
                    $response['data'] ?? []
                );
            }
            if (!empty($response) && $response['status'] === false) {
                DB::rollback();
            } else {
                DB::commit();
            }

            if ($response['code'] == 200) {
                $historyService = new HistoryService;
                $newRequestData = $request->request->add([
                    'user_id' => $response['data']->user_id,
                    'auth_user_id' => $request->user()->id,
                    'action_type' => 'Created',
                    'is_read' => '0',
                    'module' => '3',
                    'type' => '1',
                ]);

                $saveProfileHistory = (array) json_decode($historyService->saveProfileHistory($request));
            }
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception$e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add staff employment profile data insert
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @QAparam position_id int required 1
     * @QAparam manager array required [1,2]
     * @QAparam employment_type int required 1 = Full Time, 2 = Casual, 3 = Permanent Part Time
     * @QAparam employment_start_date date required 17-07-1993
     * @QAparam present_position_start_date date required 17-07-1993
     * @QAparam flexible_working_arrangements string required 1 = Yes, 2 = No
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveEmploymentInfomation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "position_id" => "required",
            "manager" => "required",
            "employment_type" => "required",
            "employment_start_date" => "required",
            "present_position_start_date" => "required",
            "flexible_working_arrangements" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $userImport = new UsersImport;
        $response = (array) $userImport->saveEmploymentInfomation($request);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add staff license and qualification profile data insert
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @QAparam do_you_have_a_drivers_licence string required 1 = Yes, 2 = No
     * @QAparam do_you_have_a_registered_vehicle string required 1 = Yes, 2 = No
     * @QAparam wwcc_number string required 111111
     * @QAparam wwcc_expiry date required 17-07-1993
     * @QAparam ndiswsc_number string required 111111
     * @QAparam ndiswsc_expiry date required 17-07-1993
     * @QAparam first_aid_certificate_expiry date required 17-07-1993
     * @QAparam cpr_expiry date required 17-07-1993
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveQualificationInfomation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "do_you_have_a_drivers_licence" => "required",
            "do_you_have_a_registered_vehicle" => "required",
            "wwcc_number" => "required",
            "wwcc_expiry" => "required",
            "ndiswsc_number" => "required",
            "ndiswsc_expiry" => "required",
            "first_aid_certificate_expiry" => "required",
            "cpr_expiry" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $userImport = new UsersImport;
        $response = (array) $userImport->saveQualificationInfomation($request);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add staff others profile data insert
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @QAparam aboriginal_or_torres_trait_islander string required 1 = Yes, 2 = No, 3 = Prefer not to say
     * @QAparam covid_vaccination_status string required 1 = Yes, 2 = No, 3 = Exempt, 4 = Decline
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveOthersInfomation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "aboriginal_or_torres_trait_islander" => "required",
            "covid_vaccination_status" => "required",
        ]);

        # validate and return msg
        if ($validator->fails()) {
            return [
                'message' => implode(',', $validator->errors()->all()),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $userImport = new UsersImport;
        $response = (array) $userImport->saveOthersInfomation($request);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * @lrd:start
     *  To Get staff profile details with staff_id
     * @lrd:end
     *
     * @QAparam staff_id string required Example 2
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStaffProfileDetails(Request $request)
    {
        $staffService = new StaffService;
        $response = json_decode($staffService->getStaffProfileDetails($request), true);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * @lrd:start
     *  To validate import all staff from uploaded csv
     * @lrd:end
     *
     * @QAparam import_file file required
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function staffImportValidate(Request $request, $customfile)
    {
        $staffService = new StaffService;
        return json_decode($staffService->staffImportValidate($request, $customfile), true);
    }

    /**
     * @lrd:start
     *  To import all staff from uploaded csv
     * @lrd:end
     *
     * @QAparam import_file file required
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function staffImport(Request $request, $customFile)
    {
        $staffService = new StaffService;
        return json_decode($staffService->staffImport($request, $customFile), true);
    }

    /**
     * @lrd:start
     *  Get payroll category details
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayrollCategory(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getPayrollCategory($request);
    }

    /**
     * @lrd:start
     *  get payroll level details
     * @lrd:end
     *
     * @QAparam cat_id int required 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayrollLevel(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getPayrollLevel($request);
    }

    /**
     * @lrd:start
     *  Get payroll pay point details
     * @lrd:end
     *
     * @QAparam cat_id int required 1
     * @QAparam level_id int required 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayrollPayPoint(Request $request)
    {
        $staffService = new StaffService;
        return $staffService->getPayrollPayPoint($request);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add staff payroll details profile data insert
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @QAparam payroll_category int required 1
     * @QAparam level int required 1
     * @QAparam paypoint int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveStaffPayrollDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "payroll_category" => "required",
            "level" => "required",
            "paypoint" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [],
            ];
        }
        $staffService = new StaffService;
        return $staffService->saveStaffPayrollDetails($request);
    }

    /**
     * @lrd:start
     * 1.3 - To save all changes profile details
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveChanges(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "user_id" => "required",
                "basic" => "required_without_all:employment,license,others,payroll",
                "employment" => "required_without_all:basic,license,others,payroll",
                "license" => "required_without_all:basic,employment,others,payroll",
                "others" => "required_without_all:basic,employment,license,payroll",
                "payroll" => "required_without_all:basic,employment,license,others",
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $userDetails = User::where(['id' => $request['user_id']])->first();

            if (!empty($userDetails) && !empty($request['basic']['status'])) {
                User::where('id', '=', $request['user_id'])->update(['status' => $request['basic']['status']]);
            }
            if ($request['basic'] && $request['basic']['primary_email']) {
                $user = User::where('id', '!=', $request['user_id'])
                    ->where('email', '=', $request['basic']['primary_email'])->first();

                if ($user) {
                    return [
                        'message' => "User already exist with given email",
                        'status' => false,
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'data' => [],
                    ];
                } else {
                    if ($userDetails->email != $request['basic']['primary_email']) {
                        $userDetails->email = $request['basic']['primary_email'];
                        $userDetails->password = "";
                        $userDetails->status = 0;
                        $userDetails->save();
                        $request->request->add(['email' => $request['basic']['primary_email']]);
                        $this->accountActivation($request);
                    }
                }
            }
            $staffService = new StaffService;
            $response = (array) json_decode($staffService->saveChanges($request));

            # Display toasted for custom validation message
            if (!empty($response['cutom_validation_message'])) {
                return $this->response(
                    $response['cutom_validation_message'],
                    $response['code'],
                    $response['data'] ?? []
                );
            }
            /**
             * Save History.
             * @lrd:start
             *  To store history of updates
             * @lrd:end
             *
             * @QAparam auth_user_id int required 1
             * @QAparam preArray  required
             * @QAparam postArray  required
             * @QAparam managerArray  required
             * @QAparam module int required
             * @QAparam type int required
             * @param  \Illuminate\Http\Request  $request
             * @return \Illuminate\Http\Response
             */

            $historyService = new HistoryService;
            $newRequestData = $request->request->add([
                'preArray' => $response['data']->preArray,
                'postArray' => $response['data']->postArray,
                'managerArray' => $response['data']->managerArray,
                'action_type' => $response['data']->action_type,
                'module' => '3',
                'type' => '1',
                'auth_user_id' => $request->user()->id,
            ]);

            $saveHistory = (array) json_decode($historyService->saveHistory($request));

            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception$e) {
            DB::rollback();
            return ['status' => false, 'error' => 'Something went wrong please try again', 'code' => $e->getCode()];
        }
    }

    /**
     * @lrd:start
     *  1.3 - To Upload Profile Image
     * @lrd:end
     *
     * @QAparam image_file file required
     * @QAparam user_id integer required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function imageUpload(Request $request)
    {
        $staffService = new StaffService;
        $response = (array) json_decode($staffService->imageUpload($request));
        if ($response['code'] == 200) {
            # To save history of updates
            $historyService = new HistoryService;
            $requestArray[] =
            array(
                'preArray' => $response['data']->preArray,
                'postArray' => $response['data']->postArray,
                'managerArray' => $response['data']->managerArray,
                'action_type' => $response['data']->action_type,
                'module' => 3,
                'type' => 2,
                'auth_user_id' => $request->user()->id,
            );
            $request->merge($requestArray);
            $saveHistory = (array) json_decode($historyService->saveFormDataHistory($request));
        }
        return $this->response(
            $response['message'] ?? $response['error'],
            $response['code'],
            $response['data'] ?? []
        );
    }

    /**
     * Update status
     * @lrd:start
     *  To update staff basic profile data insert
     * @lrd:end
     *
     * @QAparam primary_site string required Bingara
     * @QAparam status string required Active/Inactive

     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStaffStatus(Request $request)
    {
        $staffService = new StaffService;

        $response = (array) json_decode($staffService->updateStaffStatus($request));
        return $response;
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * @lrd:start
     *  To loggedin user detail
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoggedinUser(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $user = User::find($request->user()->id);
        if ($user->user_type == '1') {
            $username = $user->name;
            $useremail = $user->email;
        } else {
            $staffService = new StaffService;
            $response = json_decode($staffService->getLoggedinUser($request), true);
            $username = $response['data']['username'];
            $useremail = $response['data']['email'];
        }
        $result['username'] = $username;
        $result['useremail'] = $useremail;

        return $result;
    }

     /**
     * @lrd:start
     * To save status and manager
     * @lrd:end
     *
     * @QAparam user_id int required 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveProfileManagerAndStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "manager_id" => "required",
            "status" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }

        $userDetails = User::where(['id' => $request['user_id']])->first();

        if (!empty($userDetails)) {
            User::where('id', '=', $request['user_id'])->update(['status' => $request['status']]);
        }

        $staffService = new StaffService;
        $response = (array) json_decode($staffService->saveProfileManagerAndStatus($request));
        /**
         * Save History.
         * @lrd:start
         *  To store history of updates
         * @lrd:end
         *
         * @QAparam auth_user_id int required 1
         * @QAparam preArray  required
         * @QAparam postArray  required
         * @QAparam managerArray  required
         * @QAparam module int required
         * @QAparam type int required
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */

        $historyService = new HistoryService;
        $newRequestData = $request->request->add([
            'preArray' => $response['data']->preArray,
            'postArray' => $response['data']->postArray,
            'managerArray' =>$response['data']->managerArray,
            'action_type' => $response['data']->action_type,
            'module' => '3',
            'type' => '1',
            'auth_user_id' => $request->user()->id,
        ]);

        $saveHistory = (array) json_decode($historyService->saveHistory($request));

        return $this->response(
            $response['message'] ?? $response['error'],
            $response['code'],
            $saveHistory ?? []
        );
    }
}
