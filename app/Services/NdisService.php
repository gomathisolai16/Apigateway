<?php

namespace App\Services;

use App\Traits\ConsumeMicroserviceService;

class NdisService
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
     * Requests all ndis of a import from admin microservice
     */
    public function index($request)
    {
        return $this->performRequestUpload('/admin/ndisPriceImport', $request, $request->headers->all());
    }

    /**
     * Requests all state from admin microservice
     * with line_item_flag 1
     */
    public function getState($request)
    {
        return $this->performRequest('GET', '/admin/getState', $request->all(), $request->headers->all());
    }

    /**
     * Requests all line item from admin microservice
     * with line_item_flag 1
     */
    public function getLineItemData($request)
    {
        return $this->performRequest('POST', '/admin/getLineItemData', $request->all(), $request->headers->all());
    }

    /**
     * Requests line item detail from admin microservice
     * by line item id
     */
    public function getLineItemDetails($request)
    {
        return $this->performRequest('POST', '/admin/getLineItemDetails', $request->all(), $request->headers->all());
    }

    /**
     * Requests line item rates from admin microservice
     * by line item id & state_id
     */
    public function getLineItemRate($request)
    {
        return $this->performRequest('POST', '/admin/getLineItemRate', $request->all(), $request->headers->all());
    }

    /**
     * Requests save line detail to admin microservice
     */
    public function saveLineItemDetails($request)
    {
        return $this->performRequest('POST', '/admin/saveLineItemDetails', $request->all(), $request->headers->all());
    }
    
    /**
     * Requests save line rate to admin microservice
     */
    public function saveLineItemRates($request)
    {
        return $this->performRequest('POST', '/admin/saveLineItemRates', $request->all(), $request->headers->all());
    }
}