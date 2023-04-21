<?php
namespace App\Http\Controllers;

use App\Services\NdisService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;


class NdisServiceController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the admin microservice
     * @var NdisService
     */
    public $ndisService;

    public function __construct(NdisService $ndisService)
    {
        $this->ndisService = $ndisService;
    }

    /**
     * * @lrd:start
     *  To import all line item from uploaded csv
     * @lrd:end
     *
     * @QAparam import_file file required
     * @QAparam overwrite_given boolean optional Example 0|1
     * @QAparam overwrite_categories optional Example [1,2]
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('max_input_time', 300);
        # add auth user id to the request
        $request->request->add(['auth_user_id' => $request->user()->id]);
        $response = json_decode($this->ndisService->index($request), true);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * @lrd:start
     *  To Get all state
     * @lrd:end
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getState(Request $request)
    {
        $response = json_decode($this->ndisService->getState($request), true);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * @lrd:start
     *  To Get all line item data with state
     * @lrd:end
     *
     * @QAparam state_id string required Example 2 - NSW
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLineItemData(Request $request)
    {
        $response = json_decode($this->ndisService->getLineItemData($request), true);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * @lrd:start
     *  To Get line item details by id
     * @lrd:end
     *
     * @QAparam line_item_id string required Example 1
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLineItemDetails(Request $request)
    {
        $response = json_decode($this->ndisService->getLineItemDetails($request), true);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

    /**
     * @lrd:start
     *  To Get line item rate by state id if state selected ot fetch all
     * @lrd:end
     *
     * @QAparam line_item_id string required Example 1
     * @QAparam state_id string optional Example 2 - NSW
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLineItemRate(Request $request)
    {
        $response = json_decode($this->ndisService->getLineItemRate($request), true);
        return $this->response($response['message'], $response['code'], $response['data']);
    }

     /**
     * @lrd:start
     *  1.4 - Save line item details
     * @lrd:end
     *
     * @QAparam category string required Example 1 - Assistance with daily life
     * @QAparam registration_group string required Example 2
     * @QAparam item_code string required Example Bereavement
     * @QAparam item_name string required Example Plan Management - Bereavement Payment
     * @QAparam unit string required Example 1 - E
     * @QAparam quote string required Example 0|1 - Yes|No
     * @QAparam irregular_sil_support string required Example 0|1|2 - N|Y|NA
     * @QAparam non_f_to_f_support string required Example 0|1|2 - N|Y|NA
     * @QAparam ndia_request_report string required Example 0|1|2 - N|Y|NA
     * @QAparam short_notice_cancel string required Example 0|1|2 - N|Y|NA
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLineItemDetails(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id
        ]);
        $response = json_decode($this->ndisService->saveLineItemDetails($request), true);
        return $this->response(
            $response['message'] ?? $response['error'],
            $response['code'],
            $response['data'] ?? []
        );
    }

    /**
     * @lrd:start
     *  1.5 - Save line item rates
     * @lrd:end
     *
     * @QAparam line_item_id array required Example 1
     * @QAparam line_item_rate_id array optional Example 1 - it is required for edit
     * @QAparam state array required Example [1,2]
     * @QAparam start_date date required Example 2012-02-12 - YYYY-MM-DD
     * @QAparam end_date date optional Example 2012-02-12 - YYYY-MM-DD
     * @QAparam rate float required Example 1.00
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveLineItemRates(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id
        ]);
        $response = json_decode($this->ndisService->saveLineItemRates($request), true);
        return $this->response(
            $response['message'] ?? $response['error'],
            $response['code'],
            $response['data'] ?? []
        );
    }
}