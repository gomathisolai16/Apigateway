<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\FormBuilderService;

class FormBuilderController extends Controller
{
    use ApiResponser;
    public $formBuilderService;

    public function __construct(FormBuilderService $formBuilderService)
    {
        $this->formBuilderService = $formBuilderService;
        $this->customCatchErrorMsg = config('constants.CUSTOM_ERROR_MESSSAGE.CATCH');
    }

    /**
     *
     * @lrd:start
     *  To get modules lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getModulesList(Request $request)
    {
        $response = (array) json_decode($this->formBuilderService->getModulesList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * To get sub modules list.
     * @lrd:start
     *  To get sub modules list using module id
     * @lrd:end
     *
     * @QAparam module_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSubModulesList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "module_id" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $response = (array) json_decode($this->formBuilderService->getSubModulesList($request));
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
     * To get form fields.
     * @lrd:start
     *  To get form fields using module id
     * @lrd:end
     *
     * @QAparam module_id int required 1
     * @QAparam sub_module_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFormFields(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "module_id" => "required",
                "sub_module_id" => "required"
            ]);
            if ($validator->fails()) {
                return [
                    'message' => $validator->errors(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
            $response = (array) json_decode($this->formBuilderService->getFormFields($request));
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
}
