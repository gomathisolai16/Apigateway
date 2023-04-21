<?php

namespace App\Http\Controllers;

use App\Services\MasterDataService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MasterDataController extends Controller
{
    use ApiResponser;

    /**
     *
     * @lrd:start
     *  To get master data lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMasterListData(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $masterDataService = new MasterDataService;
        $response = (array) json_decode($masterDataService->getMasterListData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     *
     * @lrd:start
     *  To get master data types
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMasterDataType(Request $request)
    {
        $masterDataService = new MasterDataService;
        return $masterDataService->getMasterDataType($request);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add master data insert
     * @lrd:end
     *
     * @QAparam type int required 1
     * @QAparam value string required [SIL Support Worker,SIL Coordinator]
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveMasterData(Request $request)
    {
        $masterDataService = new MasterDataService;
        $response = (array) json_decode($masterDataService->saveMasterData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * Archive master record
     * @lrd:start
     *  To softdelete master record update
     * @lrd:end
     *
     * @QAparam id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function archiveMasterData(Request $request)
    {
        $masterDataService = new MasterDataService;
        $response = (array) json_decode($masterDataService->archiveMasterData($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * Update master record
     * @lrd:start
     *  To update master record update
     * @lrd:end
     *
     * @QAparam value string required SIL Coordinator
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateMasterData(Request $request)
    {
        $masterDataService = new MasterDataService;
        $response = (array) json_decode($masterDataService->updateMasterData($request));
        return $response;
    }
}
