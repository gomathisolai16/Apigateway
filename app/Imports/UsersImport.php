<?php

namespace App\Imports;

use App\Models\User;
use App\Services\StaffService;
use App\Services\RoleService;
use App\Traits\AccountActivations;
use App\Traits\ApiResponser;
use App\Traits\Otp;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Services\HistoryService;

class UsersImport implements WithHeadingRow, SkipsEmptyRows
{
    use ApiResponser;
    use AccountActivations;
    use Otp;

    /**
     * The line staff Invalid varaiable
     * @var array
     */
    private $staffInValid = [];

    public function __construct()
    {
        $this->staffUserType = config('constant.users.staff_user_type');
        $this->statusOption = config('constant.STAFF.STAFF_STATUS_OPTION');
    }

    /**
     * Staff invalid setter
     * @param {object} $staffInValid
     */
    public function setStaffInValid($staffInValid)
    {
        $this->staffInValid = $staffInValid;
    }

    /**
     * Staff invalid getter
     * @param {object} $staffInValid
     */
    public function getStaffInValid()
    {
        return $this->staffInValid;
    }

    /**
     * Store user meta data
     * @param {array} $data
     */
    public function store($data, $authUserId)
    {
        $rows = $data[0];
        foreach ($rows as $row) {
            $staff = new User();
            $staff->name = $row["first_name"];
            $staff->email = $row["primary_email"];
            $staff->user_type = $this->staffUserType;
            $staff->status = $this->getValueFromOption($this->statusOption, $row['status']);
            $staff->created_at = now();
            $staff->updated_at = null;
            $staff->save();
            $historyService = new HistoryService;
            $param = [
                'user_id' => $staff->id,
                'auth_user_id' =>  $authUserId,
                'action_type' => 'Created',
                'is_read' => '0',
                'module' => '3',
                'type' => '1',
            ];
            $historyService->saveImportHistory($param);

            # Send account activation mail if user portal access value is Yes
            if ($row['portal_access'] == "Yes") {
                $content = new Request();
                $content->email = $row['primary_email'];
                $this->accountActivation($content);
            }
        }
        return ['status' => true, 'message' => 'Save User Successfully', 'data' => []];
    }

    /**
     * Get value from array option with match search str
     * @param {array} $option
     * @param {str} $searchStr
     */
    public function getValueFromOption($option, $searchStr)
    {
        $optionTmp = array_map('strtolower', $option);
        $searchTxt = strtolower($searchStr);
        return array_search($searchTxt, $optionTmp);
    }

    /**
     * Validate primary email with existing db records
     * @param {array} $data - rows from spreadshead
     */
    public function validateEmail($data)
    {
        $rows = $data[0];
        $headerRow = array_keys($data[0][0]);
        $index = 1;
        $emailDuplicateCheckArr = [];
        foreach ($rows as $row) {
            $index++;
            $validateEmpty = array_filter(array_map('trim', $row), 'strlen');
            if (empty($validateEmpty)) {
                continue;
            }
            $arrayTemp = [];
            $row = array_map("utf8_encode", $row);
            $arrayTemp = array_combine($headerRow, $row);

            $staffmData[] = $arrayTemp;

            $checkExistEmail = User::findUserByEmail($arrayTemp['primary_email']);
            if (array_search($arrayTemp['primary_email'], $emailDuplicateCheckArr) !== false) {
                static::setValidationErr(
                    [],
                    ['Email is duplicated in the following records containing errors'],
                    $index,
                    'Primary Email Duplicated'
                );
            } elseif (!empty($checkExistEmail)) {
                static::setValidationErr(
                    [],
                    ['Staff already exist in the following records.'],
                    $index,
                    'Primary Email Exist'
                );
            }
            $emailDuplicateCheckArr[] = $arrayTemp['primary_email'];
        }
        $staffInValid = static::getStaffInValid();

        return array_values($staffInValid);
    }

    /**
     * Set invalid data to $this->setStaffInValid
     * @param {object} $options
     * @param {array} $error
     * @param {array} $row
     * @param {str} $headerName
     * @param {bool} $optionMultiple
     */
    public function setValidationErr($options, $error, $row, $headerName, $optionMultiple = false, $customIndex = null)
    {
        # Get invalid data
        $staffInvalidTmp = $this->getStaffInValid();
        $errorIndex = $customIndex ?? $headerName;
        if ($optionMultiple) {
            # if optionMultiple true add array as assoc array
            $optionTmp = $staffInvalidTmp[$errorIndex]['options'] ?? [];
            if (
                empty($optionTmp) ||
                (!empty($optionTmp) && array_search($options['id'], array_column($optionTmp, 'id')) === -1)
            ) {
                $staffInvalidTmp[$errorIndex]['options'][] = array_values($options);
            }
        } else {
            $staffInvalidTmp[$errorIndex]['options'] = array_values($options);
        }
        $staffInvalidTmp[$errorIndex]['error'] = $error;
        $rowTmp = $staffInvalidTmp[$errorIndex]['rows'] ?? [];
        if (!in_array($row, $rowTmp)) {
            $staffInvalidTmp[$errorIndex]['rows'][] = $row;
        }
        $staffInvalidTmp[$errorIndex]['header'] = $headerName;

        # set updated error value
        $this->setStaffInValid($staffInvalidTmp);
    }

    /**
     * Save staff profile basic details
     */
    public function saveProfileInfomation($request)
    {
        $collection = $request->all();
        $user = User::where('email', $collection['primary_email'])->first();
        if ($user) {
            return [
                'message' => "User already exist",
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [],
            ];
        } else {
            try {
                $staff = new User();
                $staff->name = $collection['first_name'];
                $staff->email = $collection['primary_email'];
                $staff->status = $collection['status'] ?? 1;
                $staff->user_type = $this->staffUserType;
                $staff->created_at = now();
                $staff->updated_at = now();
                $staff->save();

                if ($staff) {
                    $user_id = $staff->id;
                    $collection['user_id'] = $user_id;
                    $request->request->add(['user_id' => $user_id]);
                    if ($collection['portal_access'] === "1") {
                        $request->request->add(['email' => $collection['primary_email']]);
                        $this->accountActivation($request);
                    }
                    return $this->getStoreStaff($request);
                } else {
                    return [
                        'message' => "User Not Found",
                        'status' => false,
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'data' => [],
                    ];
                }
            } catch (\Exception$e) {
                // do task when error
                return [
                    'message' => $e->getMessage(),
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
        }
    }

    /**
     * Save staff profile employment details
     */
    public function saveEmploymentInfomation($request)
    {
        $staffService = new StaffService;
        $url = "admin/staff/saveEmploymentInfomation";
        return json_decode($staffService->saveStaffInfomation($request, $url));
    }

    /**
     * Save staff profile license and qualification details
     */
    public function saveQualificationInfomation($request)
    {
        $staffService = new StaffService;
        $url = "admin/staff/saveQualificationInfomation";
        return json_decode($staffService->saveStaffInfomation($request, $url));
    }

    /**
     * Save staff profile others details
     */
    public function saveOthersInfomation($request)
    {
        $staffService = new StaffService;
        $url = "admin/staff/saveOthersInfomation";
        return json_decode($staffService->saveStaffInfomation($request, $url));
    }

    /**
     * get staff profile all details
     */
    public function getStoreStaff($request)
    {

        $staffService = new StaffService;
        $url = "admin/staff/saveProfileInfomation";

        return json_decode($staffService->saveStaffInfomation($request, $url));
    }
    public function saveRoleBasedModulesInformation($request)
    {
       // print_r($request->all());exit;
        $roleService = new RoleService;
        $url = "admin/saveModules";
        return json_decode($roleService->saveRoleBasedModulesInformation($request, $url));
    }

    /**
     * Save staff portal access
     */
    public function savePortalAccess($request)
    {
        $collection = $request->all();

        try {
            $staff = User::where('id', $collection['user_id'])->first();
            
            if ($staff) {
                if ($collection['portal_access'] === 1) {
                    $staff->status = 1;
                    $staff->save();
                    $request->request->add(['email' => $staff['email']]);
                    $this->accountActivation($request);
                }else{
                    $staff->status = 2;
                    $staff->password = null;
                    $staff->save();
                }
                return $this->getStoreStaffPortalAccess($request);
            } else {
                return [
                    'message' => "User Not Found",
                    'status' => false,
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'data' => [],
                ];
            }
        } catch (\Exception$e) {
            // do task when error
            return [
                'message' => $e->getMessage(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [],
            ];
        }
        
    }
    
    /**
     * get staff profile portal access details
     */
    public function getStoreStaffPortalAccess($request)
    {
        $roleService = new RoleService;
        return $roleService->savePortalAccess($request);
    }
    
    /**
     * get staff profile portal access details
     */
    public function getManagerData($request)
    {
        $roleService = new RoleService;
        return json_decode($roleService->getManagerData($request));
    }

     /**
     * get staff profile portal access details for Profile
     */
    public function getProfileManagerData($request)
    {
        $roleService = new RoleService;
        return json_decode($roleService->getProfileManagerData($request));
    }

}
