<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class HistoryService
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
        $this->baseUri = config('services.communication.base_uri');
        $this->secret = config('services.communication.secret');
    }

    /**
     * Requests update history from communication microservice
     */

    public function saveHistory($request)
    {
        return $this->performRequest('POST', '/admin/history/save', $request->all(), $request->headers->all());
    }

    public function saveFormDataHistory($request)
    {
        return $this->performRequest('POST', '/admin/formdatahistory/save', $request->all(), $request->headers->all());
    }
    public function saveProfileHistory($request)
    {
        return $this->performRequest('POST', '/admin/saveprofilehistory/save', $request->all(), $request->headers->all());
    }

    /** Requests Participant history for creation  - communication micro service */
    public function saveParticipantHistory($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveparticipanthistory/save',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Requests Participant history for updation  - communication micro service */
    public function saveParticipantUpdateHistory($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveparticipantupdatehistory/save',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Requests Participant history for csv import  - communication micro service */
    public function saveParticipantImportHistory($request)
    {
        return $this->performRequest(
            'POST',
            '/admin/saveparticipantimporthistory/save',
            $request
        );
    }

    public function saveSiteHistory($request)
    {
        return $this->performRequest('POST', '/admin/savesitehistory/save', $request->all(), $request->headers->all());
    }
    public function saveUpdateSiteHistory($request)
    {
        return $this->performRequest('POST', '/admin/updatesitehistory/save', $request->all(), $request->headers->all());
    }
    public function saveImportHistory($request)
    {
        return $this->performImportRequest('POST', '/admin/savestaffcsvhistory/save', $request);
    }

     /** Requests Leave notification creation  - communication micro service */
     public function saveLeaveNotification($request)
     {
         return $this->performRequest(
             'POST',
             '/admin/saveLeaveNotification/save',
             $request->all(),
             $request->headers->all()
         );
     }

     /** Requests Leave approval/reject notification creation  - communication micro service */
     public function saveLeaveUpdateNotification($request)
     {
         return $this->performRequest(
             'POST',
             '/admin/saveLeaveUpdateNotification',
             $request->all(),
             $request->headers->all()
         );
     }
}
