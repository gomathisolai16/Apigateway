<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;
use App\Traits\ApiResponser;

class PublicHolidayService
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
    public function publicHolidayList($request)
    {
        return $this->performRequest('POST', '/publicHolidayList', $request->all(), $request->headers->all());
    }

    public function savePublicHoliday($request)
    {
        return $this->performRequest('POST', '/savePublicHoliday', $request->all(), $request->headers->all());
    }

    public function viewPublicHoliday($request)
    {
        return $this->performRequest('POST', '/viewPublicHoliday', $request->all(), $request->headers->all());
    }

    public function savePublicHolidayStatus($request)
    {
        return $this->performRequest('POST', '/savePublicHolidayStatus', $request->all(), $request->headers->all());
    }
       
}
