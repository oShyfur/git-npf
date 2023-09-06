<?php

namespace Np\Structure\Classes;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use October\Rain\Network\Http;

class OisfApiHelper
{
    private $config;
    private $base_url;
    private $http_client;

    public function __construct()
    {
        //$this->config = OisfSetting::first();

        $this->base_url = config('oisf.base_url');

        //$this->http_client = new Http();
    }

    // generate oisf token
    public function generateOisfToken()
    {


        $tokenUri = 'token/create';
        $requuestUrl = $this->base_url . $tokenUri;

        $response = Http::post($requuestUrl, function ($http) {

            $secrectKey = 'Secret ' . config('oisf.live.app_secret');
            $http->header('Authorization', $secrectKey);
        });
        //trace_log($requuestUrl);
        //trace_log($response);

        $token = null;
        if ($response->code == 200) {
            $token = json_decode($response->body);
            //Session::put('oisf_token', json_decode($response->body, true));
            //$token = $token->token;
        }

        return $token;
    }

    // get oisf token
    public function getOisfToken()
    {
        if ($this->config->token && $this->config->validity) {
            if (time() - $this->config->validity > 86400) {
                return $this->config->token;
            }
        }

        return $this->generateOisfToken();
    }

    // // get oisf office layers
    public function getOisfOfficeLayer($office_id = null)
    {
        $uri = "gen/officelayer?ministry=$office_id&xglimit=10000";
        return $this->callback($uri);
    }

    // // get oisf office origins
    public function getOisfOfficeOrigin($assoc = false)
    {
        return $this->callback('gen/officeorigin?xglimit=10000');
    }

    // // get oisf office ministries
    public function getOisfOfficeMinistry($assoc = false)
    {
        return $this->callback('gen/officeministry?xglimit=10000');
    }

    // // get oisf offices
    public function getOisfOffice($ministry_id = null, $layer_id = null)
    {
        $uri = "gen/office?status=1&xgselect=id+nameBn+name+ministry+layer+origin+division+district+upazila+union&xglimit=10000";

        if ($ministry_id)
            $uri .= '&ministry=' . $ministry_id;

        if ($layer_id)
            $uri .= '&layer=' . $layer_id;

        return $this->callback($uri);
    }

    public function getOisfEmployeeOffice($office_id = null)
    {
        $uri = "gen/empoffice?xgselect=id+designation+idNumber+employeeRecord+office+unit+organogram+joiningDate&xglimit=200";

        if ($office_id)
            $uri .= '&office=' . $office_id;

        return $this->callback($uri);
    }

    // // get oisf employees
    public function getOisfEmployee($employeeRecordId = null)
    {
        $uri = "gen/emprecord?xgselect=id+name+nameBn+mobile+email";

        if ($employeeRecordId)
            $uri .= '&id=' . $employeeRecordId;


        return $this->callback($uri);
    }

    // // get oisf employees
    // public function getOisfEmployeeById($id, $assoc = false)
    // {
    //     $employee = $this->callback('gen/emprecord?id=' . $id);
    //     return $employee[0];
    // }

    // // get oisf employees
    // public function getOisfEmployeeOffice($assoc = false)
    // {
    //     return $this->callback('gen/empoffice?xgselect=id+designation+idNumber+employeeRecord+office+unit+organogram+joiningDate&xglimit=10000');
    // }

    // // get oisf divisions
    // public function getOisfDivision($assoc = false)
    // {
    //     return $this->callback('gen/division?xglimit=10000');
    // }

    // // get oisf divisions
    // public function getOisfDistrict($assoc = false)
    // {
    //     return $this->callback('gen/district?xglimit=10000');
    // }

    // // get oisf upazilas
    // public function getOisfUpazila($assoc = false)
    // {
    //     return $this->callback('gen/upazila?xglimit=10000');
    // }

    // // get oisf unions
    // public function getOisfUnion($assoc = false)
    // {
    //     return $this->callback('gen/union?xglimit=10000');
    // }

    // // get oisf city corporations
    // public function getOisfCityCorporation($assoc = false)
    // {
    //     return $this->callback('gen/citycorporation?xglimit=10000');
    // }

    // // get oisf municipality
    // public function getOisfMunicipality($assoc = false)
    // {
    //     return $this->callback('gen/municipality?xglimit=10000');
    // }

    // // get oisf thana
    // public function getOisfThana($assoc = false)
    // {
    //     return $this->callback('gen/thana?xglimit=10000');
    // }

    // // callback function for api call
    public function callback($api_url, $assoc = false)
    {

        //$token = Session::has('oisf_token.token') ? Session::get('oisf_token.token') : '2QVN1efgbs7WQmZVjSrL1okjLx0u1C18';

        $data = '';

        // dd($this->base_url);

        if(!$this->base_url) return json_decode($data, true);

        if (Cache::has('token'))
            $token = Cache::get('token');
        else {
            $token =  $this->generateOisfToken();
            $expireAt = $token ? (((int) $token->validity) / 60) - 1 : null;
            Cache::put('token', $token, $expireAt);
        }


        if ($token) {

            $requuestUrl = $this->base_url . $api_url;


            $response = Http::get($requuestUrl, function ($http) use ($token) {

                $token = 'Bearer ' . $token->token;
                $http->header('Authorization', $token);
            });

            $data = $response->body;
        }

        return json_decode($data, true);
    }
}
