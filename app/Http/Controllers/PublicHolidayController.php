<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\PublicHolidayService;

class PublicHolidayController extends Controller
{
    use ApiResponser;

    /**
     *
     * @lrd:start
     *  To get Public Holiday lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publicHolidayList(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id
        ]);
        $publicHolidayService = new PublicHolidayService;
        $response = (array) json_decode($publicHolidayService->publicHolidayList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add publicHoliday
     * @lrd:end
     *
     * @QAparam name required mention holiday name
     * @QAparam holiday_date required "03-01-2023"
     * @QAparam state_id int required 1
     
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savePublicHoliday(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "holiday_date" => "required",
            "state_id" => "required"
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $request->request->add([
            'auth_user_id' => $request->user()->id
        ]);
        $publicHolidayService = new PublicHolidayService;
        $response = json_decode($publicHolidayService->savePublicHoliday($request));
        return($response);
        return $this->response($response->message, $response->code, $response->data);
    }

    /**
     * @lrd:start
     *  To Get holiday details by holiday id
     * @lrd:end
     *
     * @QAparam public_holiday_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function viewPublicHoliday(Request $request)
    {
        $publicHolidayService = new PublicHolidayService;
        $response = (array) json_decode($publicHolidayService->viewPublicHoliday($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * @lrd:start
     *  To save PublicHoliday Status data from public_holiday_id and status
     * @lrd:end
     *
     * @QAparam public_holiday_id int required Example 1
     * @QAparam status int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function savePublicHolidayStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "public_holiday_id" => "required",
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
        $publicHolidayService = new PublicHolidayService;
        return $publicHolidayService->savePublicHolidayStatus($request);
    }

}
