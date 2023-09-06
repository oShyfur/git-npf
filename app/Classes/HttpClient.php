<?php

namespace App\Classes;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class HttpClient
{
    public $client;
    public $baseApi;
    public $requestTimeout = 120;

    public function __construct()
    {
        $this->baseApi = config('app.baseApi');
        $client = new Client([
            'base_uri' => $this->baseApi,
            'timeout'  => $this->requestTimeout,
            'headers' => ['Accept' => 'application/json']
        ]);

        $this->client = $client;
    }

    public function send($page)
    {

        if(getenv('FULL_CACHE')=='true'){
            $redis = new \Redis();
            $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
            if(!empty(getenv('REDIS_PASSWORD')))
            $redis->auth(getenv('REDIS_PASSWORD'));
            $key = md5($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            $content = $redis->get($key);
            if(!empty($content)){
                // $content = str_replace('http://','//',str_replace('https://','//',$content));
                $content = str_replace("src='http://","src='//",str_replace('src="http://','src="//',str_replace('https://','//',$content)));
                return $content;
                exit();
            }
        }


        $request = app('request');
        $qs = http_build_query($this->commonParams($request));
        $qs .= '&' . $request->queryString;
        $uri = $page . '?' . rtrim($qs,'&');

        $response = $this->client->get($uri);
        $statusCode = $response->getStatusCode();

        if ($statusCode != '200')
            abort(500);

        $body = $response->getBody();
        $obj = json_decode($body);

        if(getenv('FULL_CACHE')=='true'){
           
            $redis->set($key, $obj->data->html);
            $redis->expire($key, 60 * (int)getenv('CACHE_TIME'));

            $keys = $redis->get(ltrim($_SERVER['HTTP_HOST'],'www.'));
            if(!empty($keys))
                $keys = json_decode($keys);
            $keys[] = $key;
            $redis->set(ltrim($_SERVER['HTTP_HOST'],'www.'), json_encode($keys));
        }
        return $obj->data->html;
    }

    public function commonParams($request)
    {
        $variables = [
            'domain' => $request->domain,
            'lang' => $request->lang,
            'protocal' => $request->protocol,
        ];

        return $variables;
    }


    public function post($page)
    {
        try {
            $request = app('request');
            $qs = http_build_query($this->commonParams($request));
            $qs .= '&' . $request->queryString;
            $uri = $page . '?' . rtrim($qs,'&');
            $postData = array_merge($request->all(),$this->commonParams($request));
            
            $response = $this->client->post($uri, array(
                'form_params' => $postData
            ));
            $statusCode = $response->getStatusCode();

            if ($statusCode != '200')
                abort(500);

            $obj = json_decode($response->getBody(), true);

            return $obj;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
