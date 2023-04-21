<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RoleService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Imports\UsersImport;
class RoleServiceController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the admin microservice
     * @var PostService
     */

    /**
     * This method requests and returns all posts of a user from post microservice
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @lrd:start
     *  To get rolesdetails
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     */
    public function getRolesData(Request $request)
    {
        $roleService = new RoleService;
        return $roleService->getRolesData($request);

    }
    /**
     * This method requests and returns all posts of a user from post microservice
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @lrd:start
     *  To get edit roles details
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     */
    public function getEditRolesData(Request $request)
    {
        $roleService = new RoleService;
        return $roleService->getEditRolesData($request);

    }
    /**
     * @lrd:start
     *  To get all role list details
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     */
    public function getAllRoleList(Request $request)
    {
        $roleService = new RoleService;
        return $roleService->getAllRoleList($request);
    }
    /**
     * @lrd:start
     *  To get modules details
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     */
    public function getModulesData(Request $request)
    {
        $roleService = new RoleService;
        return $roleService->getModulesData($request);
    }

    /**
     * @lrd:start
     *  To get permission data from module id
     * @lrd:end
     *
     * @QAparam module_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function getPermissionData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "module_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $roleService = new RoleService;
        return $roleService->getPermissionData($request);
    }

    /**
     * @lrd:start
     * save role value based on modules
     * @lrd:end
     *
     * @QAparam module_id int required Example 1
     * @QAparam role_id int required Example 1
     * @QAparam status int required Example 0 or 1
     * @param  \Illuminate\Http\Request
     */
    public function saveRoleBasedModules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "module_id" => "required",
            "role_id" => "required",
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
        $roleService = new RoleService;
        return $roleService->saveRoleBasedModulesInformation($request);
    }

    /**
     * @lrd:start
     * save user value based on modules
     * @lrd:end
     *
     * @QAparam module_id int required Example 1
     * @QAparam user_id int required Example 1
     * @QAparam status int required Example 0 or 1
     * @param  \Illuminate\Http\Request
     */
    public function saveUserBasedModules(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "module_id" => "required",
            "user_id" => "required",
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
        $roleService = new RoleService;
        return $roleService->saveUserBasedModulesInformation($request);
    }

    /**
     * @lrd:start
     * save role permission
     * @lrd:end
     *
     * @QAparam permission_id int required Example 1
     * @QAparam role_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function saveRolePermission(Request $request)
    {
        $messages = [
            "permission_id.required" => "Permission is required"
        ];
        $validator = Validator::make($request->all(), [
            "permission_id" => "required",
            "role_id" => "required",
        ], $messages);
        if ($validator->fails()) {
            return [
                'message' => implode(",", $validator->errors()->all()),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $roleService = new RoleService;
        return $roleService->saveRolePermission($request);
    }

    /**
     * @lrd:start
     * save role permission
     * @lrd:end
     *
     * @QAparam permission_id int required Example 1
     * @QAparam user_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function saveUserPermission(Request $request)
    {
        $messages = [
            "permission_id.required" => "Permission is required"
        ];
        $validator = Validator::make($request->all(), [
            "permission_id" => "required",
            "user_id" => "required",
        ], $messages);
        if ($validator->fails()) {
            return [
                'message' => implode(",", $validator->errors()->all()),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $roleService = new RoleService;
        return $roleService->saveUserPermission($request);
    }

    /**
     * @lrd:start
     *  To get permission data from module id
     * @lrd:end
     *
     * @QAparam role_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function getRolePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "role_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $roleService = new RoleService;
        return $roleService->getRolePermission($request);
    }

    /**
     * @lrd:start
     *  To get permission data from module id
     * @lrd:end
     *
     * @QAparam role_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function getModuleBasedRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "role_id" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $roleService = new RoleService;
        return $roleService->getModuleBasedRole($request);
    }

    /**
     * @lrd:start
     *  To save Role Status data from role id and status
     * @lrd:end
     *
     * @QAparam role_id int required Example 1
     * @QAparam status int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function saveRoleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "role_id" => "required",
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
        $roleService = new RoleService;
        return $roleService->saveRoleStatus($request);
    }

    /**
     * @lrd:start
     *  To save portal access data from user_id and portal_access
     * @lrd:end
     *
     * @QAparam user_id int required Example 1
     * @QAparam portal_access int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function savePortalAccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "portal_access" => "required",
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $userImport = new UsersImport;
        return $userImport->savePortalAccess($request);
    }

    /**
     * @lrd:start
     *  To get list of staff assigned for the particular role
     * @lrd:end
     *
     * @QAparam role_id int required Example 1
     * @param  \Illuminate\Http\Request
     */
    public function getRoleWithStaffStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "role_id" => "required"
        ]);
        if ($validator->fails()) {
            return [
                'message' => $validator->errors(),
                'status' => false,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'data' => [$validator->errors()],
            ];
        }
        $roleService = new RoleService;
        return $roleService->getRoleWithStaffStatus($request);
    }
 /**
     * @lrd:start
     *  Check staff is Manager/Coordinator to any active staff when disable the portal access
     * @lrd:end
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStaffByPortalAccess(Request $request)
    {
        $roleService = new RoleService;
        return $roleService->getStaffByPortalAccess($request);
        
    }
}
