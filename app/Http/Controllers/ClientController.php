<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ClientService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\HistoryService;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use App\Exports\ParticipantExport;
use App\Imports\ParticipantImport;

class ClientController extends Controller
{
    use ApiResponser;

    public $SiteService;

    public function __construct(ClientService $ClientService)
    {
        $this->ClientService = $ClientService;
        $this->customCatchErrorMsg = config('constants.CUSTOM_ERROR_MESSSAGE.CATCH');
    }

    /**
     *
     * @lrd:start
     *  To get cultural identity lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function culturalIdentityList(Request $request)
    {
        $response = (array) json_decode($this->ClientService->culturalIdentityList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To get decision makers lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function decisionMakerList(Request $request)
    {
        $response = (array) json_decode($this->ClientService->decisionMakerList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To get program lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProgramList(Request $request)
    {
        $response = (array) json_decode($this->ClientService->getProgramList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * To get list of staff or site based on program.
     * @lrd:start
     *  To get list of staff or site based on program using type
     * @lrd:end
     *
     * @QAparam type string required staff/site
     * @QAparam program_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStaffOrSite(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "type" => "required",
                "program_id" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }

            $response = (array) json_decode($this->ClientService->getStaffOrSite($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * @lrd:start
     * 1.3 - To create / Update participant details
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveParticipant(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->action_type == 'create') {
                $validator = Validator::make($request->all(), [
                    "basic_details" => "required",
                    "contact_details" => "required",
                    "emergency_contact_and_decision_maker_details" => "required",
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    "basic_details" => "required_without_all:contact_details,emergency_contact_and_decision_maker_details",
                    "contact_details" => "required_without_all:basic_details,emergency_contact_and_decision_maker_details",
                    "emergency_contact_and_decision_maker_details" => "required_without_all:basic_details,contact_details",
                ]);
            }
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $processKill = false;
            if ($request->action_type == 'create') {
                $createUser = $this->createParticipant($request);
                if ($createUser['status'] == true) {
                    $request->request->add([
                        'user_id' => $createUser['data'],
                        'auth_user_id' => $request->user()->id
                    ]);
                } else {
                    $processKill = true;
                }
            }

            if ($processKill) {
                DB::rollback();
                return [
                    'message' => 'User already exists',
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }

            $response = (array) json_decode($this->ClientService->saveParticipant($request));
            if (!empty($response) && $response['status'] === false) {
                DB::rollback();
            } else {
                DB::commit();
            }
            /** add history details for participants - History service */
            if ($response['code'] == 200) {
                $historyService = new HistoryService;
                $request->request->add([
                    'user_id' => $response['data']->user_id,
                    'auth_user_id' => $request->user()->id,
                    'action_type' => 'Created',
                    'is_read' => '0',
                    'module' => '2',
                    'type' => '1'
                ]);
                $saveParticipantHistory = (array) json_decode($historyService->saveParticipantHistory($request));
            }
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => 'Something went wrong please try again', 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * Save participant details
     */
    public function createParticipant(Request $request)
    {
        $user = User::where('email', $request->contact_details['primary_email'])->exists();
        if (!$user) {
            try {
                // Save participant in user table
                $data['name'] = $request->basic_details['first_name'];
                $data['email'] = $request->contact_details['primary_email'];
                $data['status'] = 0;
                $data['user_type'] = 3;

                $create = User::create($data);

                return [
                    'message' => 'User created successfully',
                    'status' => true,
                    'code' => Response::HTTP_OK,
                    'data' => $create->id
                ];
            } catch (\Exception $e) {
                // do task when error
                return [
                    'message' => $this->customCatchErrorMsg,
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
        } else {
            return [
                'message' => "User already exist",
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [],
            ];
        }
    }

    /**
     * List of participants.
     * @lrd:start
     *  To get list of participants
     * @lrd:end
     *
     * @QAparam program_id int 1
     * @QAparam gender int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listParticipants(Request $request)
    {
        try {
            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);
            $response = (array) json_decode($this->ClientService->listParticipants($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * View participants.
     * @lrd:start
     *  To get view of participant
     * @lrd:end
     *
     * @QAparam user_id int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewParticipant(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "participant_id" => "required",
            ]);
            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $response = (array) json_decode($this->ClientService->viewParticipant($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * @lrd:start
     * 1.6 - To save all changes participant details
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
                "basic_detail_id" => "required",
                "basic" => "required_without_all:allergic",
                "allergic" => "required_without_all:basic",
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            # update user email
            $userDetails = User::where(['id' => $request['user_id']])->first();
            if (!empty($request['basic']['contact_details']['primary_email'])) {
                $email = $request['basic']['contact_details']['primary_email'];

                $user = User::where('id', '!=', $request['user_id'])
                    ->where('email', '=', $email)->first();

                if (!empty($user)) {
                    return [
                        'message' => "User already exist with given email",
                        'status' => false,
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'data' => [],
                    ];
                } else {
                    if ($userDetails->email != $email) {
                        $userDetails->email = $email;
                        $userDetails->password = "";
                        $userDetails->status = 0;
                        $userDetails->save();
                    }
                }
            }

            $response = (array) json_decode($this->ClientService->saveChanges($request));
            /** add history details for participants - History service */
            $historyService = new HistoryService;
            $request->request->add([
                'preArray' => $response['data']->preArray,
                'postArray' => $response['data']->postArray,
                'action_type' => $response['data']->action_type,
                'module' => '2',
                'type' => '1',
                'auth_user_id' => $request->user()->id
            ]);
            $saveParticipantHistory = (array) json_decode($historyService->saveParticipantUpdateHistory($request));
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => 'Something went wrong please try again', 'code' => $e->getCode()];
        }
    }

    /*
     * Store a newly created resource in storage.
     * @lrd:start
     *  To import all participant datas from uploaded csv
     * @lrd:end
     *
     * @QAparam import_file file required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importParticipants(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "import_file" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    "message" => $validator->errors(),
                    "status" => false,
                    "code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                    "data" => [],
                ];
            }
            # export users email and id to send adminmicro service
            $filename = 'User Email With Id.csv';
            $tempFile = Excel::store(new ParticipantExport, $filename);
            $tempFileUrl = storage_path("app/$filename");
            $customfile = [];
            if ($tempFile) {
                $userFile = new UploadedFile($tempFileUrl, $filename);
                $request->files->set('user_file', $userFile);
                $customfile['user_file'] = $userFile;
            }

            # validate the participant import date from admin micro service
            $validateImportParticipantData = $this->ClientService->participantImportValidate($request, $customfile);
            $errorData = [];
            $invalidFormat = false;
            $decodeReturn = json_decode($validateImportParticipantData);
            if (!empty($decodeReturn) && $decodeReturn->status === false) {
                $errorData = $decodeReturn->data->error_data ?? [];
                $invalidFormat = $decodeReturn->invalid_format ?? false;
            }

            $response = [
                'message' => $decodeReturn->message ?? $decodeReturn->error,
                'status' => $decodeReturn->status,
                'code' => $decodeReturn->code ?? Response::HTTP_OK,
                'data' => ['error_data' => $decodeReturn->data->error_data ?? []]
            ];

            $isEmailError = array_search('Primary Email', array_column($errorData, 'header'));
            if ($isEmailError === false && !$invalidFormat) {
                # Check if primary email is not error
                $importFile = $request->file("import_file");
                $participantImport = new ParticipantImport();
                $data = Excel::toArray($participantImport, $importFile);
                $validateEmail = $participantImport->validateEmail($data);
                if (empty($validateEmail) && empty($errorData)) {
                    # Import data
                    $importRes = static::importData($request);
                    $response = [
                        'message' => $importRes['message'] ?? $importRes['error'],
                        'status' => $importRes['status'],
                        'code' => $importRes['code'] ?? Response::HTTP_OK,
                        'data' => $importRes['data']
                    ];
                } else {
                    $mergedErrorData = array_merge($validateEmail, $errorData);
                    $response = [
                        'message' => 'Unsuccessful file import.',
                        'status' => false,
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'data' => ['error_data' => $mergedErrorData,]
                    ];
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = [
                'message' => $e->getMessage(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => []
            ];
        }

        return $response;
    }

    /**
     * Import participant after validation
     * @param {object} $request - form data
     */
    public function importData(Request $request)
    {
        try {
            $importFile = $request->file('import_file');
            $importHelper = new ParticipantImport;
            $data = Excel::toArray($importHelper, $importFile);
            DB::beginTransaction();
            $authUserId = $request->user()->id;
            $saveUser = $importHelper->store($data, $authUserId);
            $response = [
                'message' => $saveUser['message'] ?? $saveUser['error'],
                'status' => $saveUser['status'],
                'code' => $saveUser['code'] ?? Response::HTTP_OK,
                'data' => []
            ];
            if (!empty($saveUser) && $saveUser['status'] === true) {
                # export users email and id to send admin micro service
                $filename = 'User Email With Id.csv';
                $tempFile = Excel::store(new ParticipantExport, $filename);
                $tempFileUrl = storage_path("app/$filename");
                $customfile = [];
                if ($tempFile) {
                    $userFile = new UploadedFile($tempFileUrl, $filename);
                    $request->files->set('user_file', $userFile);
                    $customfile['user_file'] = $userFile;
                }

                # store the participant import date from admin micro service
                $saveImportParticipantData = $this->ClientService->participantImport($request, $customfile);

                $decodeImportstate = json_decode($saveImportParticipantData);
                if (!empty($decodeImportstate) && $decodeImportstate->status === false) {
                    DB::rollback();
                } else {
                    DB::commit();
                }
                $response = [
                    'message' => $decodeImportstate->message ?? $decodeImportstate->error,
                    'status' => $decodeImportstate->status,
                    'code' => $decodeImportstate->code ?? Response::HTTP_OK,
                    'data' => []
                ];
            }
            return $response;
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    /**
     *
     * @lrd:start
     *  To get plan manage types
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function planManageTypes(Request $request)
    {
        $response = (array) json_decode($this->ClientService->planManageTypes($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /*
     * Store a newly created resource in storage.
     * @lrd:start
     *  To create service agreement plan details
     * @lrd:end
     *
     * @QAparam program_id int required
     * @QAparam basic_detail_id int required
     * @QAparam who_is_managing_the_plan int required 1-Plan managed,2-Agency managed,3-Self managed
     * @QAparam plan_start_date string required
     * @QAparam plan_end_date string required
     * @QAparam booking_start_date string required
     * @QAparam booking_end_date string required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savePlanDetails(Request $request)
    {
        try {

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "program_id" => "required",
                "participant_id" => "required",
                "who_is_managing_the_plan" => "required",
                "invoice_to" => "nullable|required_unless:who_is_managing_the_plan,2",
                "invoice_email" => "nullable|email|required_unless:who_is_managing_the_plan,2",
                "plan_management_agency" => "nullable|required_if:who_is_managing_the_plan,1",
                "plan_manager_phone" => "nullable|required_if:who_is_managing_the_plan,1",
                "plan_manager_email" => "nullable|email|required_if:who_is_managing_the_plan,1",
                "plan_start_date" => "date|required",
                "plan_end_date" => "date|after_or_equal:plan_start_date",
                "booking_start_date" => "date|after_or_equal:plan_start_date|before_or_equal:plan_end_date",
                "booking_end_date" => "date|after_or_equal:booking_start_date|before_or_equal:plan_end_date",
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->savePlanDetails($request));
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * List of plan details.
     * @lrd:start
     *  To get list of plan details
     * @lrd:end
     *
     * @QAparam participant_id int 1
     * @QAparam program_id int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listPlanDetails(Request $request)
    {
        try {
            $response = (array) json_decode($this->ClientService->listPlanDetails($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * Breadcrumb menu data.
     * @lrd:start
     *  To get breadcrumb details
     * @lrd:end
     *
     * @QAparam participant_id int 1
     * @QAparam program_id int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function breadcrumbs(Request $request)
    {
        try {
            $response = (array) json_decode($this->ClientService->breadcrumbs($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * View plan detail.
     * @lrd:start
     *  To get view of plan detail
     * @lrd:end
     *
     * @QAparam service_agreement_id required int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewPlanDetail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "service_agreement_id" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $response = (array) json_decode($this->ClientService->viewPlanDetail($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * To get list of support categories based on program.
     * @lrd:start
     *  To get list of support categories
     * @lrd:end
     *
     * @QAparam program_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSupportCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "program_id" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }

            $response = (array) json_decode($this->ClientService->getSupportCategory($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * To get list of line items based on categorys.
     * @lrd:start
     *  To get list of line items
     * @lrd:end
     *
     * @QAparam support_category_id int required 1
     * @QAparam service_agreement_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLineItem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "support_category_id" => "required",
                "service_agreement_id" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }

            $response = (array) json_decode($this->ClientService->getLineItem($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /*
     * Store a newly created resource in storage.
     * @lrd:start
     *  To create service agreement plan details
     * @lrd:end
     *
     * @QAparam service_agreement_id int required
     * @QAparam support_category_id int required
     * @QAparam line_item_id int required
     * @QAparam rate_per_unit string required
     * @QAparam units string required
     * @QAparam frequency string required
     * @QAparam rate_sub_total string required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveScheduleOfSupport(Request $request)
    {
        try {

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "service_agreement_id" => "required",
                "support_category_id" => "required",
                "rate_sub_total" => "required"
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->saveScheduleOfSupport($request));
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * List of schedule of supports.
     * @lrd:start
     *  To get list of sos
     * @lrd:end
     *
     * @QAparam service_agreement_id int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listScheduleOfSupport(Request $request)
    {
        try {
            $response = (array) json_decode($this->ClientService->listScheduleOfSupport($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * Finalize service agreement
     * @lrd:start
     *  To finalize the service agreement
     * @lrd:end
     *
     * @QAparam service_agreement_id required int 1
     * @QAparam section required string schedule_of_support|service_booking
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function finalize(Request $request)
    {
        try {
            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->finalize($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * Cron job - Archive SA
     * @lrd:start
     *  To archive service agreement when booking end date above current date
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cronJobStatusArchive(Request $request)
    {
        try {
            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->cronJobStatusArchive($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * Clone plan detail.
     * @lrd:start
     *  To get view of plan detail
     * @lrd:end
     *
     * @QAparam service_agreement_id required int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clonePlanDetail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "clone_id" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $response = (array) json_decode($this->ClientService->clonePlanDetail($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     *
     * @lrd:start
     *  To get document templates
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDocumentTemplate(Request $request)
    {
        $response = (array) json_decode($this->ClientService->getDocumentTemplate($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /*
     * Store a newly created resource in storage.
     * @lrd:start
     *  To create service document
     * @lrd:end
     *
     * @QAparam service_agreement_id int required
     * @QAparam type int required
     * @QAparam template int required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveDocument(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "service_agreement_id" => "required",
                "type" => "required",
                "template" => "required"
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->saveDocument($request));
            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * List of documents.
     * @lrd:start
     *  To get list of sa documents
     * @lrd:end
     *
     * @QAparam service_agreement_id int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listDocuments(Request $request)
    {
        try {
            $response = (array) json_decode($this->ClientService->listDocuments($request));

            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /*
     * Store a newly created resource in storage.
     * @lrd:start
     *  To update service agreement status
     * @lrd:end
     *
     * @QAparam service_agreement_id int required
     * @QAparam status int required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSAStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "service_agreement_id" => "required",
                "status" => "required",
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->updateSAStatus($request));
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /*
     * Store a newly created resource in storage.
     * @lrd:start
     *  To upload signed SA document
     * @lrd:end
     *
     * @QAparam service_agreement_document_id int required
     * @QAparam document_file string required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadSignedDocument(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "service_agreement_document_id" => "required",
                "document_file" => "required|mimes:pdf"
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->uploadSignedDocument($request));
            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /*
     * Store a newly created resource in storage.
     * @lrd:start
     *  To create service agreement service booking details
     * @lrd:end
     *
     * @QAparam service_agreement_id int required
     * @QAparam support_category_id int required
     * @QAparam line_item_id int required
     * @QAparam budget_allocated string required
     * @QAparam booking_start_date date required
     * @QAparam booking_end_date fate required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveServiceBooking(Request $request)
    {
        try {

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "service_agreement_id" => "required",
                "support_category_id" => "required",
                "line_item_id" => "required",
                "budget_allocated" => "required",
                "booking_start_date" => "required",
                "booking_end_date" => "required",
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->errors()->all(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [$validator->errors()],
                ];
            }

            $request->request->add([
                'auth_user_id' => $request->user()->id
            ]);

            $response = (array) json_decode($this->ClientService->saveServiceBooking($request));
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * List of service booking.
     * @lrd:start
     *  To get list of sa service booking
     * @lrd:end
     *
     * @QAparam service_agreement_id int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getServiceBooking(Request $request)
    {
        try {
            $response = (array) json_decode($this->ClientService->getServiceBooking($request));
            return $this->response(
                $response['message'],
                $response['code'],
                $response['data'] ?? []
            );
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $this->customCatchErrorMsg, 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }
}
