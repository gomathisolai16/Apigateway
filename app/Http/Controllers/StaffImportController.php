<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Http\Controllers\StaffServiceController;
use App\Imports\UsersImport;
use App\Services\HistoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Facades\Excel;

class StaffImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     * @lrd:start
     *  To import all staff data  from uploaded csv
     * @lrd:end
     *
     * @QAparam import_file file required
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
            $tempFile = Excel::store(new UserExport, $filename);
            $tempFileUrl = storage_path("app/$filename");
            $customfile = [];
            if ($tempFile) {
                $userFile = new UploadedFile($tempFileUrl, $filename);
                $request->files->set('user_file', $userFile);
                $customfile['user_file'] = $userFile;
            }

            # validate the staff import date from admin micro service
            $staffService = new StaffServiceController();
            $validateImportStaffData = $staffService->staffImportValidate($request, $customfile);
            $errorData = [];
            $invalidFormat = false;
            if (!empty($validateImportStaffData) && $validateImportStaffData['status'] === false) {
                $errorData = $validateImportStaffData['data']['error_data'] ?? [];
                $invalidFormat = $validateImportStaffData['invalid_format'] ?? false;
            }

            $response = [
                'message' => $validateImportStaffData['message'] ?? $validateImportStaffData['error'],
                'status' => $validateImportStaffData['status'],
                'code' => $validateImportStaffData['code'] ?? Response::HTTP_OK,
                'data' => ['error_data' => $validateImportStaffData['data']['error_data'] ?? []],
            ];

            $isEmailError = array_search('Primary Email', array_column($errorData, 'header'));
            if ($isEmailError === false && !$invalidFormat) {
                # Check if primary email is not error
                $importFile = $request->file("import_file");
                $userImport = new UsersImport();
                $data = Excel::toArray($userImport, $importFile);
                $validateEmail = $userImport->validateEmail($data);
                if (empty($validateEmail) && empty($errorData)) {
                    # Import data
                    $importRes = StaffImportController::importData($request);
                    $response = [
                        'message' => $importRes['message'] ?? $importRes['error'],
                        'status' => $importRes['status'],
                        'code' => $importRes['code'] ?? Response::HTTP_OK,
                        'data' => $importRes['data'],
                    ];
                   
                } else {
                    $mergedErrorData = array_merge($validateEmail, $errorData);
                    $response = [
                        'message' => 'Unsuccessful file import.',
                        'status' => false,
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'data' => ['error_data' => $mergedErrorData],
                    ];
                }
            }
        } catch (\Exception$e) {
            DB::rollback();
            $response = [
                'message' => $e->getMessage(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [],
            ];
        }

        return $response;
    }

    /**
     * Import staff after validation
     * @param {object} $request - form data
     */
    public function importData(Request $request)
    {
        try {
            $importFile = $request->file('import_file');
            $importHelper = new UsersImport;
            $data = Excel::toArray($importHelper, $importFile);
            DB::beginTransaction();
            $authUserId=$request->user()->id;
            $saveUser = $importHelper->store($data, $authUserId);
            $response = [
                'message' => $saveUser['message'] ?? $saveUser['error'],
                'status' => $saveUser['status'],
                'code' => $saveUser['code'] ?? Response::HTTP_OK,
                'data' => [],
            ];
            if (!empty($saveUser) && $saveUser['status'] === true) {
                # export users email and id to send adminmicro service
                $filename = 'User Email With Id.csv';
                $tempFile = Excel::store(new UserExport, $filename);
                $tempFileUrl = storage_path("app/$filename");
                $customfile = [];
                if ($tempFile) {
                    $userFile = new UploadedFile($tempFileUrl, $filename);
                    $request->files->set('user_file', $userFile);
                    $customfile['user_file'] = $userFile;
                }

                # store the staff import date from admin micro service
                $staffService = new StaffServiceController();
                $saveImportStaffData = $staffService->staffImport($request, $customfile);

                if (!empty($saveImportStaffData) && $saveImportStaffData['status'] === false) {
                    DB::rollback();
                } else {
                    DB::commit();
                }
                $response = [
                    'message' => $saveImportStaffData['message'] ?? $saveImportStaffData['error'],
                    'status' => $saveImportStaffData['status'],
                    'code' => $saveImportStaffData['code'] ?? Response::HTTP_OK,
                    'data' => [],
                ];
            }
            return $response;
        } catch (\Exception$e) {
            DB::rollback();
            return ['status' => false, 'error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }
}
