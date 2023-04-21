<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ConsumeMicroserviceService;

class CheckServiceController extends Controller
{

    use ConsumeMicroserviceService;

    /**
     * The base uri to consume post microservice
     * @var string
     */
    public $baseUri;

    /**
     * Authorization secret to pass to post microservice
     * @var string
     */
    public $secret;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->baseUri = config('services.communication.base_uri');
        $this->secret = config('services.communication.secret');
    }

    public function checkService(Request $request)
    {
        return $this->performRequest("POST", '/checkService', $request->all(), $request->headers->all());
    }
}
