<?php

namespace App\Http\Controllers;

use App\Services\LeaveService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\HistoryService;

class LeaveController extends Controller
{
    use ApiResponser;

    /**
     *
     * @lrd:start
     *  To get staff and manager data
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStaffNameListData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "staff_id" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->getStaffNameListData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To get leave list data
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLeaveListData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "staff_id" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->getLeaveListData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To save leave details
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @QAparam manager_id int required 1
     * @QAparam leave_type int required 1
     * @QAparam start_date date required 17-07-1993
     * @QAparam end_date date required 17-07-1993
     * @QAparam start_time string required 11:11
     * @QAparam end_time string required 11:11
     * @QAparam reason date required 17-07-1993
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveLeaveDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "staff_id" => "required",
            "manager_id" => "required",
            "leave_type" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "start_time" => "required",
            "end_time" => "required",
            "reason" => "required",
            'hours_prior' => "required",
            'hours_requested' => "required",
            'hours_active' => "required",
            'hours_balance' => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->saveLeaveDetails($request));

        /** add history details and notification for leave application */
        if ($response['code'] == 200) {
            $historyService = new HistoryService;
            $request->request->add([
                'staff_id' => $request->staff_id,
                'manager_id'=> $request->manager_id,
                'mail_data'=>$response['data'],
                'auth_user_id' => $request->user()->id,
                'action_type' => 'General',
                'is_read' => '0',
                'module' => '6',
                'type' => '2'
            ]);
            $saveLeaveNotification = (array) json_decode($historyService->saveLeaveNotification($request));
        }

        # Print custom Validation message
         if(!empty($response['cutom_validation_message'])) {
            return $this->response(
                $response['cutom_validation_message'],
                $response['code'],
                $response['data'] ?? []
            );
        }

        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To get Roster Details
     * @lrd:end
     *
     * @QAparam start_date date required 17-07-1993
     * @QAparam end_date date required 17-07-1993
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRosterDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "start_date" => "required",
            "end_date" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->getRosterDetails($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To get Leave Details Data
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @QAparam leave_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLeaveDetailsData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "staff_id" => "required",
            "leave_id" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->getLeaveDetailsData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To Reporting Staff Manager Data
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReportingStaffManagerData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "staff_id" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->getReportingStaffManagerData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

     /**
     *
     * @lrd:start
     *  To Reporting Staff Manager Data
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @QAparam leave_type int required 1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLeaveBalances(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "staff_id" => "required",
            "leave_type" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->getLeaveBalances($request));

        return $this->response($response['message'] ?? "", $response['code'], $response['data'] ?? []);
    }

     /**
     *
     * @lrd:start
     *  To Delete Leave Details
     * @lrd:end
     *
     * @QAparam leave_id int required 1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteLeaveDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "leave_id" => "required",
   
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->deleteLeaveDetails($request));

        return $this->response($response['message'] ?? "", $response['code'], $response['data'] ?? []);
    }

    /**
     *
     * @lrd:start
     *  To get leave list data
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRequestLeaveListData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "staff_id" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->getRequestLeaveListData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  leave Application Confirmation
     * @lrd:end
     *
     * @QAparam staff_id int required 1
     * @QAparam request_type string required approval | reject | cancel
     * @QAparam notes string required Test
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function leaveApplicationConfirmation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "leave_id" => "required",
            "request_type" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }

        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->leaveApplicationConfirmation($request));

        /** add history details and notification for leave application */
        if ($response['code'] == 200) {
            $historyService = new HistoryService;
            $request->request->add([
                'mail_data' => $response['data'],
                'auth_user_id' => $request->user()->id,
                'action_type' => 'General',
                'is_read' => '0',
                'module' => '6',
                'type' => '2'
            ]);
            $saveLeaveNotification = (array) json_decode($historyService->saveLeaveUpdateNotification($request));
        }

        $msg = $response['message'] ?? "";
        return $this->response($msg, $response['code'], []);
    }

        /**
     * @lrd:start
     *  To Upload Leave attachment document
     * @lrd:end
     *
     * @QAparam pdf_file file required
     * @QAparam user_id integer required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveLeaveDocument(Request $request)
    {
        $leaveService = new LeaveService;
        $response = (array) json_decode($leaveService->saveLeaveDocument($request));
        
        return $this->response(
            $response['message'] ?? $response['error'],
            $response['code'],
            $response['data'] ?? []
        );
    }
}
