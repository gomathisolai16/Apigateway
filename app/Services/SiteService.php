<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;
use App\Traits\ApiResponser;

class SiteService
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
    public function siteList($request)
    {
        return $this->performRequest('GET', '/siteList', $request->all(), $request->headers->all());
    }

    public function saveSite($request)
    {
        return $this->performRequest('POST', '/saveSite', $request->all(), $request->headers->all());
    }

    public function viewSite($request)
    {
        return $this->performRequest('POST', '/viewSite', $request->all(), $request->headers->all());
    }

    public function saveSiteCoordinator($request)
    {
        return $this->performRequest('POST', '/saveSiteCoordinator', $request->all(), $request->headers->all());
    }

    public function getCoordinatorList($request)
    {
        return $this->performRequest('POST', '/getCoordinatorList', $request->all(), $request->headers->all());
    }
    public function siteSaveChanges($request)
    {
        return $this->performRequest('POST', 'admin/site/siteSaveChanges', $request->all(), $request->headers->all());
    }
    public function checkSiteDetails($request)
    {
        return $this->performRequest('POST', 'admin/site/checkSiteDetails', $request->all(), $request->headers->all());
    }
}
