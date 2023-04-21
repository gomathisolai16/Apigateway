<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;
use App\Traits\ApiResponser;

class ClientService
{
    use ConsumeMicroserviceService;

    /**
     * The base uri to consume admin microservice
     * @var string
     */
    public $baseUri;

    /**
     * Authorization secret to pass to admin microservice
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.admin.base_uri');
        $this->secret = config('services.admin.secret');
    }

    /**
     * Requests cultural identity from admin microservice
     */
    public function culturalIdentityList($request)
    {
        return $this->performRequest('GET', '/culturalIdentityList', $request->all(), $request->headers->all());
    }

    /**
     * Requests decision makers from admin microservice
     */
    public function decisionMakerList($request)
    {
        return $this->performRequest('GET', '/decisionMakerList', $request->all(), $request->headers->all());
    }

    /**
     * Requests progrm from admin microservice
     */
    public function getProgramList($request)
    {
        return $this->performRequest('GET', '/getProgramList', $request->all(), $request->headers->all());
    }

    /**
     * Requests staff or site list from admin microservice
     */
    public function getStaffOrSite($request)
    {
        return $this->performRequest('POST', '/getStaffOrSite', $request->all(), $request->headers->all());
    }

    /**
     * Requests to save participants from admin microservice
     */
    public function saveParticipant($request)
    {
        return $this->performRequest('POST', '/saveParticipant', $request->all(), $request->headers->all());
    }

    /**
     * Requests participants list from admin microservice
     */
    public function listParticipants($request)
    {
        return $this->performRequest('POST', '/listParticipants', $request->all(), $request->headers->all());
    }

    /**
     * Requests participants list from admin microservice
     */
    public function viewParticipant($request)
    {
        return $this->performRequest('POST', '/viewParticipant', $request->all(), $request->headers->all());
    }

    /**
     * Requests to save all changes participants from admin microservice
     */
    public function saveChanges($request)
    {
        return $this->performRequest('POST', '/participants/saveChanges', $request->all(), $request->headers->all());
    }

    /**
     * Requests to validate import participant from admin microservice
     */
    public function participantImportValidate($request, $customfile)
    {
        return $this->performRequestUpload(
            '/participantImportValidate',
            $request,
            $request->headers->all(),
            $customfile
        );
    }

    /**
     * Requests to import participant from admin microservice
     */
    public function participantImport($request, $customFile)
    {
        return $this->performRequestUpload('/participantImport', $request, $request->headers->all(), $customFile);
    }

    /**
     * Requests plan manage types from admin microservice
     */
    public function planManageTypes($request)
    {
        return $this->performRequest('GET', '/serviceAgreement/planManageTypes', $request->all(), $request->headers->all());
    }

    /**
     * Requests to save plan details from admin microservice
     */
    public function savePlanDetails($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/savePlanDetails', $request->all(), $request->headers->all());
    }

    /**
     * Requests to list plan details from admin microservice
     */
    public function listPlanDetails($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/listPlanDetails', $request->all(), $request->headers->all());
    }

    /**
     * Requests to list breadcrumb details from admin microservice
     */
    public function breadcrumbs($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/breadcrumbs', $request->all(), $request->headers->all());
    }

    /**
     * Requests to view plan detail from admin microservice
     */
    public function viewPlanDetail($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/viewPlanDetail', $request->all(), $request->headers->all());
    }

    /**
     * Requests to list support categories from admin microservice
     */
    public function getSupportCategory($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/getSupportCategory', $request->all(), $request->headers->all());
    }

    /**
     * Requests to list line items from admin microservice
     */
    public function getLineItem($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/getLineItem', $request->all(), $request->headers->all());
    }

    /**
     * Requests to save schedule of support from admin microservice
     */
    public function saveScheduleOfSupport($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/saveScheduleOfSupport', $request->all(), $request->headers->all());
    }

    /**
     * Requests to list schedule of support from admin microservice
     */
    public function listScheduleOfSupport($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/listScheduleOfSupport', $request->all(), $request->headers->all());
    }

    /**
     * Requests to finalize service agreement from admin microservice
     */
    public function finalize($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/finalize', $request->all(), $request->headers->all());
    }

    /**
     * Requests to archive service agreement from admin microservice
     */
    public function cronJobStatusArchive($request)
    {
        return $this->performRequest('GET', '/serviceAgreement/cronJobStatusArchive', $request->all(), $request->headers->all());
    }

    /**
     * Requests to clone plan detail from admin microservice
     */
    public function clonePlanDetail($request)
    {
        return $this->performRequest(
            'POST',
            '/serviceAgreement/clonePlanDetail',
            $request->all(),
            $request->headers->all()
        );
    }

    /**
     * Requests to archive service agreement from admin microservice
     */
    public function getDocumentTemplate($request)
    {
        return $this->performRequest('GET', '/serviceAgreement/getDocumentTemplate', $request->all(), $request->headers->all());
    }

    /**
     * Requests to save document from admin microservice
     */
    public function saveDocument($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/saveDocument', $request->all(), $request->headers->all());
    }

    /**
     * Requests to save document from admin microservice
     */
    public function listDocuments($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/listDocuments', $request->all(), $request->headers->all());
    }

    /**
     * Requests to update SA status from admin microservice
     */
    public function updateSAStatus($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/updateSAStatus', $request->all(), $request->headers->all());
    }

    /**
     * Requests to update SA signed document from admin microservice
     */
    public function uploadSignedDocument($request)
    {
        return $this->performRequestUpload('/serviceAgreement/uploadSignedDocument', $request, $request->headers->all());
    }

    /**
     * Requests to save service booking from admin microservice
     */
    public function saveServiceBooking($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/saveServiceBooking', $request->all(), $request->headers->all());
    }
    /**
     * Requests to save service booking from admin microservice
     */
    public function getServiceBooking($request)
    {
        return $this->performRequest('POST', '/serviceAgreement/getServiceBooking', $request->all(), $request->headers->all());
    }
}
