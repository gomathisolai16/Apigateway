<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;
use App\Traits\ApiResponser;

class CommunicationService
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
        $this->baseUri = config('services.communication.base_uri');
        $this->secret = config('services.communication.secret');
    }

    /**
     * Requests all filter option parameters from admin microservice
     */
    public function notificationList($request) {
        return $this->performRequest('POST', '/notificationList', $request->all(), $request->headers->all());
    }

    public function notificationDetail($request) {
        return $this->performRequest('POST', '/notificationDetail', $request->all(), $request->headers->all());
    }
    public function historyDetails($request) {
        return $this->performRequest('POST', '/historyDetails', $request->all(), $request->headers->all());
    }
    public function participantHistoryDetails($request) {
        return $this->performRequest('POST', '/participantHistoryDetails', $request->all(), $request->headers->all());
    }
}
