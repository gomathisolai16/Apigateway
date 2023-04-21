<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

trait ConsumeMicroserviceService
{
    /**
     * Send request to any service
     * @param $method
     * @param $requestUrl
     * @param array $formParams
     * @param array $headers
     * @return string
     */
    public function performRequest($method, $requestUrl, $formParams = [], $headers = [])
    {
        
        $client = new Client([
            'base_uri'  =>  $this->baseUri,
        ]);
        
        $response = $client->request($method, $requestUrl, [
            'form_params' => $formParams,
            'headers'     => ['Authorization' => $this->secret],
        ]);

        
        return $response->getBody()->getContents();
    }

     /**
     * Send upload request to any service
     * @param $method - POST
     * @param $requestUrl
     * @param array $formParams
     * @param array $headers
     * @return string
     */
    public function performRequestUpload($requestUrl, $request, $headers = [], $customFiles = [])
    {

        $formParams = $request->allFiles();
        $client = new Client([
            'base_uri'  =>  $this->baseUri,
        ]);
        $multiPart = [];
        foreach ($formParams as $shortKey => $param) {
            $data = [];
            if ($shortKey) {
                $uploadFile = $request->file($shortKey);
                $data =  [
                    'name'     => $shortKey,
                    'contents' => file_get_contents($uploadFile->getRealPath()),
                    'filename' => $uploadFile->getClientOriginalName(),
                    'Content-type' => 'multipart/form-data',
                    'headers'  => array('Content-Type' => mime_content_type($uploadFile->getRealPath()))
                ];
            } else {
                $data = [
                    'name'     => $shortKey,
                    'contents' => $param,
                ];
            }
            $multiPart[] = $data;
        }

        # prepend only values
        foreach ($request->all() as $formKey => $param) {
            if (!$request->hasFile($formKey)) {
                $data = [
                    'name'     => $formKey,
                    'contents' => $param,
                ];
                $multiPart[] = $data;
            }
        }

        # add custom file not from request
        if (!empty($customFiles)) {
            foreach ($customFiles as $shortKey => $customFile) {
                $multiPart[] =  [
                    'name'     => $shortKey,
                    'contents' => file_get_contents($customFile->getRealPath()),
                    // 'contents' => fopen($customFile->getRealPath(), 'r'),
                    'filename' => $customFile->getClientOriginalName(),
                    'Content-type' => 'multipart/form-data',
                    'headers'  => array('Content-Type' => mime_content_type($customFile->getRealPath()))
                ];
            }
        }

        $response = $client->request('POST', $requestUrl, [
            'multipart' => $multiPart,
            'headers'     => ['Authorization' => $this->secret],
            'connect_timeout' => 300,
            'read_timeout' => 300,
            'timeout' => 300,
        ]);
        
        return $response->getBody()->getContents();
    }


    /**
     * Send staff load upload request to any service
     * @param $method - POST
     * @param $requestUrl
     * @param array $collection
     * @param array $headers
     * @return string
     */

    
    public function performImportRequest($method, $requestUrl, $collection)
    {
     
        $client = new Client([
            'base_uri'  => $this->baseUri,
        ]);
     
        $response = $client->request($method, $requestUrl, [
            'json' => $collection,
            'headers'     => ['Authorization' => $this->secret]
        ]);
 
     return $response->getBody()->getContents();
 
    
    }

    public function performStaffRequest($method, $requestUrl, $formParams = [], $headers = [])
    {
      
        $client = new Client([
            'base_uri'  =>  $this->baseUri,
        ]);
        
        $response = $client->request($method, $requestUrl, [
            'form_params' => $formParams,
            'headers'     => ['Authorization' => $this->secret],
        ]);

        return $response->getBody()->getContents();
      
    }




}