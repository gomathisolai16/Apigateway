<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class StaffService
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

    /**
     * Requests all posts of a user  from post microservice
     */
    public function index($collection)
    {
        return $this->performImportRequest('POST', 'adminstaff/staffload', $collection);
    }

    public function getStaffDetailsData($request)
    {
        return $this->performStaffRequest('POST', '/admin/getStaffDetailsData', $request->all(), $request->headers->all());
    }
    public function getStaffDetails($request)
    {
        return $this->performStaffRequest('POST', '/admin/getStaffDetails', $request->all(), $request->headers->all());
    }

    public function getProgramData($request)
    {
        return $this->performRequest('GET', '/admin/getProgramData', $request->all(), $request->headers->all());
    }
    public function getStaffByProgram($request)
    {
        return $this->performRequest('POST', '/admin/getStaffByProgram', $request->all(), $request->headers->all());
    }
    public function getStaffByStatus($request)
    {
        return $this->performRequest('POST', '/admin/getStaffByStatus', $request->all(), $request->headers->all());
    }
    public function getSiteData($request)
    {
        return $this->performRequest('POST', '/admin/getSiteData', $request->all(), $request->headers->all());
    }

    public function getPositionData($request)
    {
        return $this->performRequest('GET', '/admin/getPositionData', $request->all(), $request->headers->all());
    }

    public function getAttributeData($request)
    {
        return $this->performRequest('GET', '/admin/getAttributeData', $request->all(), $request->headers->all());
    }

    public function saveStaffInfomation($request, $url)
    {
        return $this->performRequest('POST', $url, $request->all(), $request->headers->all());
    }

    /**
     * Requests staff detail from admin microservice
     * by staff id
     */
    public function getStaffProfileDetails($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/staff/getStaffProfileDetails',
            $request->all(),
            $request->headers->all()
        );
    }

    /**
     * Requests to validate import staff from admin microservice
     */
    public function staffImportValidate($request, $customfile)
    {
        return $this->performRequestUpload(
            '/admin/staffImportValidate',
            $request,
            $request->headers->all(),
            $customfile
        );
    }

    /**
     * Requests to import staff from admin microservice
     */
    public function staffImport($request, $customFile)
    {
        return $this->performRequestUpload('/admin/staffImport', $request, $request->headers->all(), $customFile);
    }

    public function getPayrollCategory($request)
    {
        return $this->performRequest('GET', '/admin/staff/getPayrollCat', $request->all(), $request->headers->all());
    }
    public function getPayrollLevel($request)
    {
        return $this->performRequest('POST', '/admin/staff/getPayrollLevel', $request->all(), $request->headers->all());
    }
    public function getPayrollPayPoint($request)
    {
        return $this->performRequest('POST', '/admin/staff/getPayrollPoint', $request->all(), $request->headers->all());
    }
    public function saveStaffPayrollDetails($request)
    {
        return $this->performRequest('POST', 'admin/staff/saveStaffPayrollDetails', $request->all(), $request->headers->all());
    }
    public function saveChanges($request)
    {
        return $this->performRequest('POST', 'admin/staff/saveChanges', $request->all(), $request->headers->all());
    }

    /**
     * Requests upload profile image upload from admin microservice
     */
    public function imageUpload($request)
    {
        return $this->performRequestUpload('/admin/staff/saveProfileImage', $request, $request->headers->all());
    }

    public function updateStaffStatus($request)
    {
        return $this->performRequest('POST', 'admin/staff/updateStaffStatus', $request->all(), $request->headers->all());
    }

    /**
     * Requests loggedin user detail from admin microservice
     */
    public function getLoggedinUser($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/staff/getLoggedinUser',
            $request->all(),
            $request->headers->all()
        );
    }
/**
 * Requests save status and manager to admin microservice
 */
    public function saveProfileManagerAndStatus($request)
    {
        return $this->performRequest('POST', 'admin/staff/saveProfileManagerAndStatus', $request->all(), $request->headers->all());
    }
}
