<?php

namespace App\Http\Controllers;

use App\Services\RosterScheduleService;
use App\Traits\AccountActivations;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RosterScheduleController extends Controller
{
    use ApiResponser;
    use AccountActivations;

    public function __construct()
    {
        $this->customCatchErrorMsg = config('constants.CUSTOM_ERROR_MESSSAGE.CATCH');
    }

    /**
     *
     * @lrd:start
     *  Save roster schedule details
     * @lrd:end
     *
     * @QAparam roster_id required Free text
     * @QAparam start_date string required 2023-10-01
     * @QAparam number_of_weeks int required 12
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveRosterScheduleDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
                "start_date" => "required",
                "number_of_weeks" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterScheduleService = new RosterScheduleService;
            $response = (array) json_decode($rosterScheduleService->saveRosterScheduleDetails($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
       
    }

    /**
     *
     * @lrd:start
     *  get Active Roster Template List
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getActiveRosterTemplateList(Request $request)
    {
        try {
            
            $rosterScheduleService = new RosterScheduleService;
            $response = (array) json_decode($rosterScheduleService->getActiveRosterTemplateList($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
       
    }

    /**
     *
     * @lrd:start
     *  get Roster Schedule Details
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRosterScheduleDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
                "start_date" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterScheduleService = new RosterScheduleService;
            $response = (array) json_decode($rosterScheduleService->getRosterScheduleDetails($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
       
    }

    /**
     *
     * @lrd:start
     * view Roster Schedule Shifts
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewRosterScheduleShifts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
                "start_date" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterScheduleService = new RosterScheduleService;
            $response = (array) json_decode($rosterScheduleService->viewRosterScheduleShifts($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
       
    }

    /*
     * Update Roster Schedule Details.
     * @lrd:start
     *  To update roster shift support items
     * @lrd:end
     *
     * @QAparam roster_id int required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRosterScheduleDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
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
            
            $rosterScheduleService = new RosterScheduleService;
            $response = (array) json_decode($rosterScheduleService->updateRosterScheduleDetails($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }

    }
           
    /**
     *
     * @lrd:start
     * view Roster Schedule Shifts
     * @lrd:end
     *
     * @QAparam shift_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewScheduleShift(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "shift_id" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterScheduleService = new RosterScheduleService;
            $response = (array) json_decode($rosterScheduleService->viewScheduleShift($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
        
    }
           
    /**
     *
     * @lrd:start
     * publish Roster Schedule Shifts by Ids
     * @lrd:end
     *
     * @QAparam shift_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publishShiftById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "shift_id" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterScheduleService = new RosterScheduleService;
            $response = (array) json_decode($rosterScheduleService->publishShiftById($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
        
    }

}
