<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;
use App\Traits\ApiResponser;

class LeaveService
{
    use ConsumeMicroserviceService;

    /**
     * The base uri to consume admin microservice
     * @var string
     */
    public $baseUri;

    /**
     * Authorization secret to pass to staff microservice
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.admin.base_uri');
        $this->secret = config('services.admin.secret');
    }

    /**
     * Requests all filter option parameters from admin microservice
     */
    public function getStaffNameListData($request)
    {
        return $this->performRequest('POST', 'admin/getStaffNameListData', $request->all(), $request->headers->all());
    }

    public function getLeaveListData($request)
    {
        return $this->performRequest('POST', 'admin/getLeaveListData', $request->all(), $request->headers->all());
    }

    public function saveLeaveDetails($request)
    {
        return $this->performRequest('POST', 'admin/saveLeaveDetails', $request->all(), $request->headers->all());
    }

    public function getRosterDetails($request)
    {
        return $this->performRequest('POST', 'admin/getRosterDetails', $request->all(), $request->headers->all());
    }

    public function getLeaveDetailsData($request)
    {
        return $this->performRequest('POST', 'admin/getLeaveDetailsData', $request->all(), $request->headers->all());
    }

    public function getReportingStaffManagerData($request)
    {
        return $this->performRequest('POST', 'admin/getReportingStaffManagerData', $request->all(), $request->headers->all());
    }
    
    public function getLeaveBalances($request)
    {
        return $this->performRequest('POST', 'admin/getLeaveBalances', $request->all(), $request->headers->all());
    }

    public function deleteLeaveDetails($request)
    {
        return $this->performRequest('POST', 'admin/deleteLeaveDetails', $request->all(), $request->headers->all());
    }
    
    public function getRequestLeaveListData($request)
    {
        return $this->performRequest('POST', 'admin/getRequestLeaveListData', $request->all(), $request->headers->all());
    }

    public function leaveApplicationConfirmation($request)
    {
        return $this->performRequest('POST', 'admin/leaveApplicationConfirmation', $request->all(), $request->headers->all());
    }

    /**
     * Requests upload pdf document for leave application
     */
    public function saveLeaveDocument($request)
    {
        return $this->performRequestUpload('/admin/saveLeaveDocument', $request, $request->headers->all());
    }
}
