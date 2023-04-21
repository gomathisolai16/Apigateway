<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class RosterService
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

    /** Request for Get all roster details for listing page */
    public function getRosterDetailsData($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getRosterDetailsData',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for get participant details with respect to site id */
    public function getParticipantSiteData($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getParticipantSiteData',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for get employee position to append */
    public function getEmployeePosition($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getEmployeePosition',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for get staff details for site id */
    public function getStaffSiteDetails($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getStaffSiteDetails',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for get shift address to append */
    public function getShiftAddressData($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getShiftAddressData',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for save roster templates */
    public function saveRosterTemplates($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveRosterTemplates',
            $request->all(),
            $request->headers->all()
        );
    }

    /** get data to view calender slots in selected views */
    public function getRosterViews($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getRosterViews',
            $request->all(),
            $request->headers->all()
        );
    }

    /** get view data for basic details */
    public function getRosterBasicDetails($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getRosterBasicDetails',
            $request->all(),
            $request->headers->all()
        );
    }

    /** save shift for roster details */
    public function saveShiftRosterDetails($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveShiftRosterDetails',
            $request->all(),
            $request->headers->all()
        );
    }

    /** save status for roster details */
    public function saveRosterStatus($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveRosterStatus',
            $request->all(),
            $request->headers->all()
        );
    }

    /** get staff list for roster adding shifts */
    public function searchStaffShiftRoster($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/searchStaffShiftRoster',
            $request->all(),
            $request->headers->all()
        );
    }

    /** get staff list for roster adding shifts */
    public function viewRosterShifts($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/viewRosterShifts',
            $request->all(),
            $request->headers->all()
        );
    }

    /** get staff list for roster adding shifts */
    public function getRosterFilterValues($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getRosterFilterValues',
            $request->all(),
            $request->headers->all()
        );
    }

     /** get attribute list for roster basic details */
     public function getRosterAttributeData($request)
     {
         return $this->performRequest(
             'POST',
             '/admin/getRosterAttributeData',
             $request->all(),
             $request->headers->all()
         );
     }
    /** copy the shift for selected values in calendar */
    public function copyShiftCalender($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/copyShiftCalender',
            $request->all(),
            $request->headers->all()
        );
    }

    /** copy and save the shift for selected values */
    public function saveAndCopyShiftData($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveAndCopyShiftData',
            $request->all(),
            $request->headers->all()
        );
    }
    /** copy all shift data */
    public function copyAllShiftsData($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/copyAllShiftsData',
            $request->all(),
            $request->headers->all()
        );
    }

    /**
     * Requests to view roster shift from admin microservice
     */
    public function viewShift($request)
    {
        return $this->performRequest('POST', '/rosters/viewShift', $request->all(), $request->headers->all());
    }

    /**
     * Requests to save roster shift support items from admin microservice
     */
    public function saveShiftSupportItems($request)
    {
        return $this->performRequest('POST', '/rosters/saveShiftSupportItems', $request->all(), $request->headers->all());
    }
    
    /** validate before save and copy */
    public function validateSaveAndCopy($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/validateSaveAndCopy',
            $request->all(),
            $request->headers->all()
        );
    }

        /** update shift for roster details */
        public function updateShiftRosterDetails($request)
        {            
            return $this->performRequest(
                'POST',
                '/rosters/updateShiftRosterDetails',
                $request->all(),
                $request->headers->all()
            );
        }
    /** get staff list for roster adding shifts based on position*/
    public function searchStaffShiftRosterByPosition($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/searchStaffShiftRosterByPosition',
            $request->all(),
            $request->headers->all()
        );
    }
    /** Delete shift */
    public function deleteShiftById($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/deleteShiftById',
            $request->all(),
            $request->headers->all()
        );
    }

    /**
     * Requests to update roster shift support items from admin microservice
     */
    public function updateShiftSupportItems($request)
    {
        return $this->performRequest('POST', '/rosters/updateShiftSupportItems', $request->all(), $request->headers->all());
    }
}
