<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;
use App\Traits\ApiResponser;

class FilterService
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
    public function getFilterParams($request) {
        return $this->performRequest('POST', '/admin/getFilterParams', $request->all(), $request->headers->all());
    }

    /**
     * Requests filter results from admin microservice
     */
    public function getFilterResult($request) {
        return $this->performRequest("POST",'/admin/getFilterResult', $request->all(), $request->headers->all());
    }
}
