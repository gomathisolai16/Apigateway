<?php

namespace App\Imports;

use App\Models\User;
use App\Services\StaffService;
use App\Services\RoleService;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Services\HistoryService;

class ParticipantImport implements WithHeadingRow, SkipsEmptyRows
{
    use ApiResponser;

    /**
     * The line participant Invalid varaiable
     * @var array
     */
    private $participantInValid = [];

    public function __construct()
    {
        $this->participantUserType = config('constant.users.participant_user_type');
        $this->statusOption = config('constant.general_status_option');
    }

    /**
     * Participant invalid setter
     * @param {object} $participantInValid
     */
    public function setParticipantInValid($participantInValid)
    {
        $this->participantInValid = $participantInValid;
    }

    /**
     * Participant invalid getter
     * @param {object} $participantInValid
     */
    public function getParticipantInValid()
    {
        return $this->participantInValid;
    }

    /**
     * Store user meta data
     * @param {array} $data
     */
    public function store($data, $authUserId)
    {
        $rows = $data[0];
        foreach ($rows as $row) {
            $participant = new User();
            $participant->name = $row["first_name"];
            $participant->email = $row["primary_email"];
            $participant->user_type = $this->participantUserType;
            $participant->status = $this->getValueFromOption($this->statusOption, $row['status']);
            $participant->created_at = now();
            $participant->updated_at = null;
            $participant->save();
            $historyService = new HistoryService;
            $param = [
                'id' => $participant->id,
                'auth_user_id' =>  $authUserId,
                'action_type' => 'Created',
                'is_read' => '0',
                'module' => '2',
                'type' => '1',
            ];
            $historyService->saveParticipantImportHistory($param);
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

            $participantmData[] = $arrayTemp;

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
                    ['Participant already exist in the following records.'],
                    $index,
                    'Primary Email Exist'
                );
            }
            $emailDuplicateCheckArr[] = $arrayTemp['primary_email'];
        }
        $participantInValid = static::getParticipantInValid();

        return array_values($participantInValid);
    }

    /**
     * Set invalid data to $this->setParticipantInValid
     * @param {object} $options
     * @param {array} $error
     * @param {array} $row
     * @param {str} $headerName
     * @param {bool} $optionMultiple
     */
    public function setValidationErr($options, $error, $row, $headerName, $optionMultiple = false, $customIndex = null)
    {
        # Get invalid data
        $participantInvalidTmp = $this->getParticipantInValid();
        $errorIndex = $customIndex ?? $headerName;
        if ($optionMultiple) {
            # if optionMultiple true add array as assoc array
            $optionTmp = $participantInvalidTmp[$errorIndex]['options'] ?? [];
            if (
                empty($optionTmp) ||
                (!empty($optionTmp) && array_search($options['id'], array_column($optionTmp, 'id')) === -1)
            ) {
                $participantInvalidTmp[$errorIndex]['options'][] = array_values($options);
            }
        } else {
            $participantInvalidTmp[$errorIndex]['options'] = array_values($options);
        }
        $participantInvalidTmp[$errorIndex]['error'] = $error;
        $rowTmp = $participantInvalidTmp[$errorIndex]['rows'] ?? [];
        if (!in_array($row, $rowTmp)) {
            $participantInvalidTmp[$errorIndex]['rows'][] = $row;
        }
        $participantInvalidTmp[$errorIndex]['header'] = $headerName;

        # set updated error value
        $this->setParticipantInValid($participantInvalidTmp);
    }

    /**
     * Save participant profile basic details
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
                $participant = new User();
                $participant->name = $collection['first_name'];
                $participant->email = $collection['primary_email'];
                $participant->status = $collection['status'] ?? 1;
                $participant->user_type = $this->participantUserType;
                $participant->created_at = now();
                $participant->updated_at = now();
                $participant->save();

                if ($participant) {
                    $user_id = $participant->id;
                    $collection['user_id'] = $user_id;
                    $request->request->add(['user_id' => $user_id]);
                } else {
                    return [
                        'message' => "User Not Found",
                        'status' => false,
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'data' => [],
                    ];
                }
            } catch (\Exception $e) {
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
}
