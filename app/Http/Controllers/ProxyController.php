<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    public function proxyGeoserverWFS(Request $request)
    {
        // Generate a cache key based on the request query
        $cacheKey = 'geoserver_response_' . md5(http_build_query($request->query()));

        // Check if the response is already cached
        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey), 200)
                ->header('Content-Type', 'application/json');
        }

        // If not cached, make the request to Geoserver
        $client = new Client();
        $url = 'http://kinaryaalamraya.com:8080/geoserver/simtaruBalangan/ows';

        $response = $client->get($url, [
            'query' => $request->query()
        ]);

        $responseBody = (string) $response->getBody();
        
        // Cache the response for future requests
        Cache::put($cacheKey, $responseBody, now()->addMinutes(10)); // Adjust the cache duration as needed

        return response($responseBody, $response->getStatusCode())
            ->header('Content-Type', $response->getHeaderLine('Content-Type'));
    }

    public function proxyGeoserverWMS(Request $request)
    {
        // Generate a cache key based on the request query
        $cacheKey = 'geoserver_response_' . md5(http_build_query($request->query()));

        // Check if the response is already cached
        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey), 200)
                ->header('Content-Type', 'application/json');
        }

        // If not cached, make the request to Geoserver
        $client = new Client();
        $url = 'http://kinaryaalamraya.com:8080/geoserver/simtaruBalangan/wms';

        $response = $client->get($url, [
            'query' => $request->query()
        ]);

        $responseBody = (string) $response->getBody();
        
        // Cache the response for future requests
        Cache::put($cacheKey, $responseBody, now()->addMinutes(10)); // Adjust the cache duration as needed

        return response($responseBody, $response->getStatusCode())
            ->header('Content-Type', $response->getHeaderLine('Content-Type'));
    }

    
}
