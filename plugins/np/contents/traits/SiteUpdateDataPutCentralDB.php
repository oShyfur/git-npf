<?php

namespace Np\Contents\Traits;

use Illuminate\Support\Facades\Log;
use Np\Contents\Scopes\SiteScope;
use Np\Structure\Classes\NP;
use Http;

trait SiteUpdateDataPutCentralDB
{
    public static function bootSiteUpdateDataPutCentralDB()
    {
        // static::addGlobalScope(new SiteScope);
        static::extend(function ($model){
            //relation to site
            $model->belongsTo['site'] = ['Np\Structure\Models\Site'];

            $model->bindEvent('model.afterSave', function () use ($model) {
                $model->updateCentralDatabaseSiteData($model);
            });

        });

    }

    public function updateCentralDatabaseSiteData($model)
    {
        $domains = $model->domains;
        $fqdn  = $domains[0]['fqdn'];
        // foreach($domains as $domain){
        //     $fqdn = $domain->fqdn;
        // }
        // echo "<pre>";print_r($domains[0]['fqdn']);die();
        $site = $model->getAttributes();

        // Replace "www." with an empty string
        $subdomain = str_replace('www.', '', $fqdn);
        // Assuming you have the data parameters in the $site array
        $data = [
            'id'   => $site['id'],
            'uuid' => $site['uuid'],
            'fqdn' => $subdomain,
        ];

        // Build the query string for the URL
        $queryString = http_build_query($data);

        // Construct the URL with the query string
        $url = 'http://data-migration.test/api/update/site?' . $queryString;

        // Make the HTTP GET request
        $response = Http::get($url);

        // $res = json_decode($result->body(), true);
        // dd($response->body);
    }

}
