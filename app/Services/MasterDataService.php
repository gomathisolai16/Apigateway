<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class MasterDataService
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

    public function getMasterDataType($request)
    {
        return $this->performRequest('GET', '/getMasterDataType', $request->all(), $request->headers->all());
    }

    public function saveMasterData($request)
    {
        return $this->performRequest('POST', '/saveMasterData', $request->all(), $request->headers->all());
    }

    public function archiveMasterData($request)
    {
        return $this->performRequest('POST', '/archiveMasterData', $request->all(), $request->headers->all());
    }

    public function getMasterListData($request)
    {
        return $this->performRequest('GET', '/getMasterListData', $request->all(), $request->headers->all());
    }
    public function updateMasterData($request)
    {
        return $this->performRequest('POST', '/updateMasterData', $request->all(), $request->headers->all());
    }
}
