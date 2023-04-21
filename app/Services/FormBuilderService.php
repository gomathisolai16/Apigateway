<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class FormBuilderService
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

    /** Request for Get modules list */
    public function getModulesList($request)
    {
        return $this->performRequest(
            'GET',
            '/formBuilder/getModulesList',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for Get sub modules list by module_id */
    public function getSubModulesList($request)
    {
        return $this->performRequest(
            'POST',
            '/formBuilder/getSubModulesList',
            $request->all(),
            $request->headers->all()
        );
    }

    /** Request for Get form fields */
    public function getFormFields($request)
    {
        return $this->performRequest(
            'POST',
            '/formBuilder/getFormFields',
            $request->all(),
            $request->headers->all()
        );
    }
}
