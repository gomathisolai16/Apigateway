<?php

namespace App\Http\Controllers;

use App\Services\ProgressNoteTemplateService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProgressNoteTemplateController extends Controller
{
    use ApiResponser;

    /**
     *
     * @lrd:start
     *  To get progress lists
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function progressTemplateList(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $progressNoteTemplateService = new ProgressNoteTemplateService;
        $response = (array) json_decode($progressNoteTemplateService->progressTemplateList($request));
        $msg = $response['message'] ?? "";
        $data = $response['data'] ?? [];
        return $this->response($msg, $response['code'], $data);
    }

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add progress
     * @lrd:end
     * @QAparam program_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveProgressNoteTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "program_id" => "required",
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
        $progressNoteTemplateService = new ProgressNoteTemplateService;
        $response = json_decode($progressNoteTemplateService->saveProgressNoteTemplate($request));
        
        return $this->response(
            $response->message,
            $response->code,
            $response->data ?? []
        );
    }

    /**
     * @lrd:start
     *  To Get program option list
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProgramOptions(Request $request)
    {
        $progressNoteTemplateService = new ProgressNoteTemplateService;
        return $progressNoteTemplateService->getProgramOptions($request);
    }

     /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To add progress question with options
     * @lrd:end
     * @QAparam template_id int required 1
     * @QAparam updated_question_order_no int required 1
     * @QAparam question_type int required 1
     * @QAparam condition int required 1
     * @QAparam no_of_option int required 1
     * @QAparam question int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveProgressQuestionDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "template_id" => "required",
            "updated_question_order_no" => "required",
            "question_type" => "required",
            "condition" => "required",
            "question" => "required",
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
        $progressNoteTemplateService = new ProgressNoteTemplateService;
        $response = json_decode($progressNoteTemplateService->saveProgressQuestionDetails($request));
        
        return $response;
    }

    /**
     * @lrd:start
     * Get progress template details.
     * @lrd:end
     * @QAparam template_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProgressTemplateDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "template_id" => "required"
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
        $progressNoteTemplateService = new ProgressNoteTemplateService;
        $response = json_decode($progressNoteTemplateService->getProgressTemplateDetails($request));
        
        return $this->response(
            $response->message,
            $response->code,
            $response->data ?? []
        );
    }

    /**
     * @lrd:start
     *  To save Progress template Status data from template_id and status
     * @lrd:end
     *
     * @QAparam template_id int required Example 1
     * @QAparam status int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function saveProgressTemplateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "template_id" => "required",
            "program_id" => "required",
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
        $progressNoteTemplateService = new ProgressNoteTemplateService;
        return $progressNoteTemplateService->saveProgressTemplateStatus($request);
    }

    /**
     * @lrd:start
     * Get question order list.
     * @lrd:end
     * @QAparam template_id int required 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getQuestionOrderList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "template_id" => "required"
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
        $progressNoteTemplateService = new ProgressNoteTemplateService;
        $response = json_decode($progressNoteTemplateService->getQuestionOrderList($request));
        
        return $this->response(
            $response->message,
            $response->code,
            $response->data ?? []
        );
    }
}
