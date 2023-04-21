<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class RoleService
{
    use ConsumeMicroserviceService;
    /**
     * The base uri to consume admin microservice
     * @var string
     */
    public $baseUri;

    /**
     * Authorization secret to pass to post microservice
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.admin.base_uri');
        $this->secret = config('services.admin.secret');
    }

    public function getRolesData($request)
    {
        return $this->performStaffRequest('GET', '/admin/getRolesData', $request->all(), $request->headers->all());
    }
    public function getEditRolesData($request)
    {
        return $this->performStaffRequest('GET', '/admin/getEditRolesData', $request->all(), $request->headers->all());
    }

    public function getAllRoleList($request)
    {
        return $this->performStaffRequest('GET', '/admin/getAllRoleList', $request->all(), $request->headers->all());
    }
    public function getModulesData($request)
    {
        return $this->performStaffRequest('GET', '/admin/getModulesData', $request->all(), $request->headers->all());
    }
    public function getPermissionData($request)
    {
        return $this->performStaffRequest('POST', '/admin/getPermissionData', $request->all(), $request->headers->all());
    }
    public function saveRoleBasedModulesInformation($request)
    {
        return $this->performRequest('POST', '/admin/saveModules', $request->all(), $request->headers->all());
    }
    public function saveUserBasedModulesInformation($request)
    {
        return $this->performRequest('POST', '/admin/saveUserModules', $request->all(), $request->headers->all());
    }
    public function saveRolePermission($request)
    {
        return $this->performRequest('POST', '/admin/saveRolePermission', $request->all(), $request->headers->all());
    }
    public function saveUserPermission($request)
    {
        return $this->performRequest('POST', '/admin/saveUserPermission', $request->all(), $request->headers->all());
    }
    public function getRolePermission($request)
    {
        return $this->performRequest('POST', '/admin/getRolePermission', $request->all(), $request->headers->all());
    }
    public function getModuleBasedRole($request)
    {
        return $this->performRequest('POST', '/admin/getModuleBasedRole', $request->all(), $request->headers->all());
    }
    public function saveRoleStatus($request)
    {
        return $this->performRequest('POST', '/admin/saveRoleStatus', $request->all(), $request->headers->all());
    }
    public function savePortalAccess($request)
    {
        return $this->performRequest('POST', '/admin/savePortalAccess', $request->all(), $request->headers->all());
    }
    public function getUserPermissions($request)
    {
        return $this->performRequest('POST', '/admin/getUserPermissions', $request->all(), $request->headers->all());
    }
    public function checkRolePermissions($request)
    {
        return $this->performRequest('POST', '/admin/checkRolePermissions', $request->all(), $request->headers->all());
    }
    public function getRoleWithStaffStatus($request)
    {
        return $this->performRequest('POST', '/admin/getRoleWithStaffStatus', $request->all(), $request->headers->all());
    }
    public function getManagerData($request)
    {
        return $this->performRequest('POST', '/admin/getManagerData', $request->all(), $request->headers->all());
    }
    public function getStaffByPortalAccess($request)
    {
        return $this->performRequest('POST', '/admin/getStaffByPortalAccess', $request->all(), $request->headers->all());
    }
    
     /**
     * Requests manager from admin microservice
     */
    public function getProfileManagerData($request)   {
        return $this->performRequest('POST','/admin/getProfileManagerData', $request->all(), $request->headers->all());
    }
}
