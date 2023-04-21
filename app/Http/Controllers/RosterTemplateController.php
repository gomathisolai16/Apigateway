<?php

namespace App\Http\Controllers;

use App\Services\RosterService;
use App\Traits\AccountActivations;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RosterTemplateController extends Controller
{
    use ApiResponser;
    use AccountActivations;

    public function __construct(RosterService $RosterService)
    {
        $this->RosterService = $RosterService;
        $this->customCatchErrorMsg = config('constants.CUSTOM_ERROR_MESSSAGE.CATCH');
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  Get all Roster template data for listing
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRosterDetails(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getRosterDetailsData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }
    /**
     * @lrd:start
     * Get Participant Details with respect to site details
     * @lrd:end
     *
     * @QAparam site_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function getParticipantSiteData(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getParticipantSiteData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * @lrd:start
     * Get employee position to list data
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getEmployeePosition(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getEmployeePosition($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }
    /** @lrd:start
     *  Get staff details respect to site
     * @lrd:end
     * @QAparam site_id int required 1
     * @QAparam preferred_staff int required 1
     * @QAparam blocked_staff int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStaffSiteDetails(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getStaffSiteDetails($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }
    /** @lrd:start
     *  Get shift address to append
     * @lrd:end
     * @QAparam program_id int required 1
     * @QAparam site_id int required 1
     * @QAparam participant_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getShiftAddressData(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getShiftAddressData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  Save roster templates
     * @lrd:end
     *
     * @QAparam name required Free text
     * @QAparam program int required 1
     * @QAparam site int required 1
     * @QAparam participants int required 1
     * @QAparam frequency int required 1
     * @QAparam staff_position int required 1
     * @QAparam preferred_staff int 1
     * @QAparam blocked_staff int 1
     * @QAparam mandatory_attributes int 1
     * @QAparam optional_attributes int 1
     * @QAparam shiftaddress required
     * @QAparam sleepover required yes or no
     * @QAparam sleepover_starttime time 12:30
     * @QAparam sleepover_starttime time 12:30
     * @QAparam duration int 8
     * @QAparam geolocation_activity yes or no
     * @QAparam permit-check yes or no
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveRosterTemplates(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->saveRosterTemplates($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /** @lrd:start
     *  Get data for calender slots with respect to roster id
     * @lrd:end
     * @QAparam type int required 1
     * @QAparam roster_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRosterViews(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getRosterViews($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /** @lrd:start
     *  Get data for view basic details
     * @lrd:end
     * @QAparam roster_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRosterBasicDetails(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getRosterBasicDetails($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  Save roster templates
     * @lrd:end
     *
     * @QAparam roster_id int required 1
     * @QAparam week int required 1
     * @QAparam day int required 1
     * @QAparam shift_type int required 1
     * @QAparam shift_starttime int required 1
     * @QAparam shift_endtime int required 1
     * @QAparam staff_position int required 1
     * @QAparam participant int 1
     * @QAparam staff int 1
     * @QAparam no_of_staff int 1
     * @QAparam break int 1
     * @QAparam break_starttime int 1
     * @QAparam break_endtime required
     * @QAparam sleepover required yes or no
     * @QAparam sleepover_starttime time 12:30
     * @QAparam sleepover_starttime time 12:30
     * @QAparam sleepover_duration time 12:30
     * @QAparam break_duration time 12:30
     * @QAparam shift_duration time 12:30
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveShiftRosterDetails(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->saveShiftRosterDetails($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  Save status for roster templates
     * @lrd:end
     *
     * @QAparam roster_id required Free text
     * @QAparam status int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveRosterStatus(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->saveRosterStatus($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     * get staff details for adding shift
     * @lrd:end
     *
     * @QAparam roster_id required int 1
     * @QAparam staff_position_id optional int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchStaffShiftRoster(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->searchStaffShiftRoster($request));
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
     * view shift details for roster
     * @lrd:end
     *
     * @QAparam roster_id required Free text
     * @QAparam positionid optional int 1
     * @QAparam staffid optional int 1
     * @QAparam participantid optional int 1
     * @QAparam type optional int 1,2,3
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewRosterShifts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
                "type" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->viewRosterShifts($request));
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
     * get filter values for roster template
     * @lrd:end
     *
     * @QAparam roster_id required int 1
     * @QAparam staff_position_id optional int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRosterFilterValues(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->getRosterFilterValues($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /** @lrd:start
     *  Get attribute details
     * @lrd:end
     * @QAparam mandatory_attributes int required 1
     * @QAparam optional_attributes int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRosterAttributeData(Request $request)
    {
        $rosterService = new RosterService;
        $response = (array) json_decode($rosterService->getRosterAttributeData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }
    /**
     *
     * @lrd:start
     * copy the shift for selected values in calendar
     * @lrd:end
     *
     * @QAparam roster_id required int 1
     * @QAparam shift_id required int 1
     * @QAparam copy_shift_data required array()
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function copyShiftCalender(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
                "shift_id" => "required",
                "copy_shift_data" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->copyShiftCalender($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * @lrd:start
     *  copy and Save roster templates
     * @lrd:end
     *
     * @QAparam copy_shift_data required array()
     * @QAparam roster_id int required 1
     * @QAparam week int required 1
     * @QAparam day int required 1
     * @QAparam shift_type int required 1
     * @QAparam shift_starttime int required 1
     * @QAparam shift_endtime int required 1
     * @QAparam staff_position int required 1
     * @QAparam participant int 1
     * @QAparam staff int 1
     * @QAparam no_of_staff int 1
     * @QAparam break int 1
     * @QAparam break_starttime int 1
     * @QAparam break_endtime required
     * @QAparam sleepover required yes or no
     * @QAparam sleepover_starttime time 12:30
     * @QAparam sleepover_starttime time 12:30
     * @QAparam sleepover_duration time 12:30
     * @QAparam break_duration time 12:30
     * @QAparam shift_duration time 12:30
     * @QAparam copy_shift_data required array()
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAndCopyShiftData(Request $request)
    {
        try {
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->saveAndCopyShiftData($request));
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
     * copy all shift for selected week
     * @lrd:end
     *
     * @QAparam roster_id required int 1
     * @QAparam week required int 1
     * @QAparam copy_shift_data required array()
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function copyAllShiftsData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
                "week" => "required",
                "copy_shift_data" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->copyAllShiftsData($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * View roster shift.
     * @lrd:start
     *  To get view of roster shift
     * @lrd:end
     *
     * @QAparam shift_id required int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewShift(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "shift_id" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $response = (array) json_decode($this->RosterService->viewShift($request));

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
     *  To create roster shift support items
     * @lrd:end
     *
     * @QAparam shift_id int required
     * @QAparam participants string required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveShiftSupportItems(Request $request)
    {
        try {

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "shift_id" => "required",
                "participants" => "required",
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

            $response = (array) json_decode($this->RosterService->saveShiftSupportItems($request));
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
     * @lrd:start
     *  validate before save and copy
     * @lrd:end
     *
     * @QAparam roster_id int required 1
     * @QAparam week int required 1
     * @QAparam day int required 1
     * @QAparam shift_type int required 1
     * @QAparam shift_starttime int required 1
     * @QAparam shift_endtime int required 1
     * @QAparam staff_position int required 1
     * @QAparam participant int 1
     * @QAparam staff int 1
     * @QAparam no_of_staff int 1
     * @QAparam break int 1
     * @QAparam break_starttime int 1
     * @QAparam break_endtime required
     * @QAparam sleepover required yes or no
     * @QAparam sleepover_starttime time 12:30
     * @QAparam sleepover_starttime time 12:30
     * @QAparam sleepover_duration time 12:30
     * @QAparam break_duration time 12:30
     * @QAparam shift_duration time 12:30
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validateSaveAndCopy(Request $request)
    {
        try {
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->validateSaveAndCopy($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /**
     * @lrd:start
     *  delete shift
     * @lrd:end
     * @QAparam shift_id int required 1
     
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteShiftById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "shift_id" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }

            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->deleteShiftById($request));
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
     * get staff details for adding shift based on position
     * @lrd:end
     *
     * @QAparam roster_id required int 1
     * @QAparam staff_position_id optional int 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchStaffShiftRosterByPosition(Request $request)
    {
       try {
            $validator = Validator::make($request->all(), [
                "roster_id" => "required",
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $rosterService = new RosterService;
            $response = (array) json_decode($rosterService->searchStaffShiftRosterByPosition($request));
            $msg = $response['message'] ?? "";
            $data = $response['data'] ?? [];
            return $this->response($msg, $response['code'], $data);
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
    }

    /*
     * Update existing shift resource in storage.
     * @lrd:start
     *  To edit shift items
     * @lrd:end
     *
     * @QAparam shift_id int required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateShiftRosterDetails(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "shift_id" => "required",
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
            $response = (array) json_decode($this->RosterService->updateShiftRosterDetails($request));
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
     * Update existing shift resource in storage.
     * @lrd:start
     *  To update roster shift support items
     * @lrd:end
     *
     * @QAparam shift_id int required
     * @QAparam participants string required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateShiftSupportItems(Request $request)
    {
        try {

            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                "shift_id" => "required",
                "participants" => "required",
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

            $response = (array) json_decode($this->RosterService->updateShiftSupportItems($request));
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

}
