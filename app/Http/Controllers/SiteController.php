<?php

namespace App\Http\Controllers;

use App\Services\HistoryService;
use App\Services\SiteService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    use ApiResponser;

    /**
     *
     * @lrd:start
     *  To get site lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function siteList(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $siteService = new SiteService;
        $response = (array) json_decode($siteService->siteList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add site
     * @lrd:end
     *
     * @QAparam name required 13 Hector Unique
     * @QAparam program_id int required 1
     * @QAparam state_id int required 1
     * @QAparam cost required Free text
     * @QAparam address required
     * @QAparam email required abc@abc.com
     * @QAparam mobile_number int required 1234567890
     * @QAparam fixed_line_number int optional 1234567890
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "program_id" => "required",
            "state_id" => "required",
            "cost" => "required",
            "address" => "required",
            "email" => "required|email",
            "mobile_number" => "required|digits:10",
            "fixed_line_number" => "nullable|digits:10",
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
            'auth_user_id' => $request->user()->id,
        ]);
        $siteService = new SiteService;
        $response = json_decode($siteService->saveSite($request));

        if ($response->code == 200) {
            $historyService = new HistoryService;
            $newRequestData = $request->request->add([
                'site_id' => $response->data->id,
                'auth_user_id' => $request->user()->id,
                'action_type' => 'Created',
                'is_read' => '0',
                'module' => '5',
                'type' => '1',
            ]);

            $saveSiteHistory = (array) json_decode($historyService->saveSiteHistory($request));
        }
        return $this->response(
            $response->message,
            $response->code,
            $response->data ?? []
        );
    }

    /**
     * @lrd:start
     *  To Get site details by site id
     * @lrd:end
     *
     * @QAparam site_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function viewSite(Request $request)
    {
        $siteService = new SiteService;
        $response = (array) json_decode($siteService->viewSite($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add site
     * @lrd:end
     *
     * @QAparam site_id required 1
     * @QAparam service_coordinator_id int required 1
     * @QAparam senior_support_worked_id int required 1
     * @QAparam project_manager_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSiteCoordinator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "site_id" => "required",
            "service_coordinator_id" => "required",
            "senior_support_worked_id" => "required",
            "project_manager_id" => "required",
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
            'auth_user_id' => $request->user()->id,
        ]);
        $siteService = new SiteService;
        $response = json_decode($siteService->saveSiteCoordinator($request));
        return $this->response($response->message, $response->code, $response->data);
    }

    /**
     *
     * @lrd:start
     *  To get coordinators list
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCoordinatorList(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $siteService = new SiteService;
        $response = (array) json_decode($siteService->getCoordinatorList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * @lrd:start
     * 1.3 - To save all changes site details
     * @lrd:end
     *
     * @QAparam site_id int required 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function siteSaveChanges(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "site_id" => "required",
            "basic" => "required_without_all:coordinator",
            "coordinator" => "required_without_all:basic",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }

        $siteService = new SiteService;
        $response = (array) json_decode($siteService->siteSaveChanges($request));
      
        if (!empty($response['data']) && !empty($response['status']) && $response['status'] === true) {
            $historyService = new HistoryService;
            $newRequestData = $request->request->add([
                'preArray' => $response['data']->preArray,
                'postArray' => $response['data']->postArray,
                'action_type' => $response['data']->action_type,
                'module' => '5',
                'type' => '1',
                'auth_user_id' => $request->user()->id,
            ]);

            $saveUpdateSiteHistory = (array) json_decode($historyService->saveUpdateSiteHistory($request));
        }
            return $this->response(
                $response['message'] ?? $response['error'],
                $response['code'],
                $response['data'] ?? []
            );
    }

     /**
     *
     * @lrd:start
     *  To check site details
     * @lrd:end
     * 
     * @QAparam site_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkSiteDetails(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            "site_id" => "required",
        ]);

        if ($validator->fails()) {
            return [
                'message' => $validator->errors()->all(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $siteService = new SiteService;
        $response = (array) json_decode($siteService->checkSiteDetails($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

}
