<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{

    /**
     * Building success response
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($msg, $code = Response::HTTP_OK, $data = [])
    {
        $resPhrase = "";
        $resStatus = "";
        switch ($code) {
            case Response::HTTP_OK:
                $resPhrase = "Ok";
                $resStatus = true;
                break;
            case Response::HTTP_CREATED:
                $resPhrase = "Created";
                $resStatus = true;
                break;
            case Response::HTTP_ACCEPTED:
                $resPhrase = "Accepted";
                $resStatus = true;
                break;
            case Response::HTTP_NO_CONTENT:
                $resPhrase = "No Content";
                $resStatus = true;
                break;
            case Response::HTTP_UNPROCESSABLE_ENTITY:
                $resPhrase = "Unprocessible Entity";
                $resStatus = false;
                break;
            case Response::HTTP_BAD_REQUEST:
                $resPhrase = "Bad Request";
                $resStatus = false;
                break;
            case Response::HTTP_UNAUTHORIZED:
                $resPhrase = "Unauthorized";
                $resStatus = false;
                break;
            case Response::HTTP_FORBIDDEN:
                $resPhrase = "Forbidden";
                $resStatus = false;
                break;
            case Response::HTTP_NOT_FOUND:
                $resPhrase = "Not Found";
                $resStatus = false;
                break;
            case Response::HTTP_SERVICE_UNAVAILABLE:
                $resPhrase = "Service Unavailable";
                $resStatus = false;
                break;
            case Response::HTTP_INTERNAL_SERVER_ERROR:
                $resPhrase = "Internal Server Error";
                $resStatus = false;
                break;
            case Response::HTTP_NOT_IMPLEMENTED:
                $resPhrase = "Not Implemented";
                $resStatus = false;
                break;
            case Response::HTTP_BAD_GATEWAY:
                $resPhrase = "Bad Gateway";
                $resStatus = false;
                break;
            case Response::HTTP_CONFLICT:
                $resPhrase = "Record alredy exist";
                $resStatus = false;
                break;
            default:
                # code...
                break;
        }
        $response = [
            "code" => $code,
            "phrase" => $resPhrase,
            "status" => $resStatus,
            "message" => $msg,
            "data" => $data
        ];

        return \response()->json($response, $code);
    }
}
