<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class RosterScheduleService
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

    /** Request for save Roster Schedule Details */
    public function saveRosterScheduleDetails($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveRosterScheduleDetails',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for get Active Roster Template List */
    public function getActiveRosterTemplateList($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getActiveRosterTemplateList',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for get Roster Schedule Details */
    public function getRosterScheduleDetails($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/getRosterScheduleDetails',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for view Roster Schedule Shifts */
    public function viewRosterScheduleShifts($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/viewRosterScheduleShifts',
            $request->all(),
            $request->headers->all()
        );
    }

    /**
     * Requests to update roster shift support items from admin microservice
     */
    public function updateRosterScheduleDetails($request)
    {
        return $this->performRequest('POST', '/admin/updateRosterScheduleDetails', $request->all(), $request->headers->all());
    }
    
    /** Request for view Roster Schedule Shifts */
    public function viewScheduleShift($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/viewScheduleShift',
            $request->all(),
            $request->headers->all()
        );
    }
    
    /** Request for publish Roster Schedule Shifts */
    public function publishShiftById($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/publishShiftById',
            $request->all(),
            $request->headers->all()
        );
    }

}
