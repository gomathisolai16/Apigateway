<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;
use App\Traits\ApiResponser;

class ProgressNoteTemplateService
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
    public function progressTemplateList($request)
    {
        return $this->performRequest('GET', '/progressTemplateList', $request->all(), $request->headers->all());
    }

    public function saveProgressNoteTemplate($request)
    {
        return $this->performRequest('POST', '/saveProgressNoteTemplate', $request->all(), $request->headers->all());
    }

    public function getProgramOptions($request)
    {
        return $this->performRequest('GET', '/getProgramOptions', $request->all(), $request->headers->all());
    }

    public function saveProgressQuestionDetails($request)
    {
        return $this->performRequest('POST', '/saveProgressQuestionDetails', $request->all(), $request->headers->all());
    }
    public function getProgressTemplateDetails($request)
    {
        return $this->performRequest('POST', '/getProgressTemplateDetails', $request->all(), $request->headers->all());
    }
    public function saveProgressTemplateStatus($request)
    {
        return $this->performRequest('POST', '/saveProgressTemplateStatus', $request->all(), $request->headers->all());
    }
    public function getQuestionOrderList($request)
    {
        return $this->performRequest('POST', '/getQuestionOrderList', $request->all(), $request->headers->all());
    }
}
