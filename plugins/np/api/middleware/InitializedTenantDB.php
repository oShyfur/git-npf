<?php

namespace Np\Api\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use October\Rain\Support\Facades\Flash;
use Np\Structure\Models\Domain;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class InitializedTenantDB
{
    /**
     * The Laravel Application
     *
     * @var Application
     */
    protected $app;

    /**
     * Create a new middleware instance.
     *
     * @param  Application $app
     * @return void
     */
    public function __construct()
    {
        //$this->app = $app;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {

        // $token = $request->header('X-API-KEY');
        // if ($token != 123)
        //     return response('Not valid token provider.', 401);


        $domain = input('domain');

        $lang = input('lang');

        if ($domain)
            $this->initDB($request, $domain, $lang);

        return $next($request);
    }

    public function initDB($request, $host, $lang)
    {

        $domain = Domain::with('site')->where('fqdn', $host)->first();
        if ($domain) {
            $site = $domain->site;

            $lang = $site->getSiteLang($lang);
            $cluster = $site->cluster;

            // For Site title, Site line 1, Site line 2
            $name = $site->name;
            $site_title_line1 = $site->site_title_line1;
            $site_title_line2 = $site->site_title_line2;

            $attrData = DB::table('rainlab_translate_attributes')->select('attribute_data')->where('model_id', $site->id)->Where('model_type', 'like', '%site%')->where('locale',$lang )->first();
            if(!empty($attrData) && $attrData!=""){
                $attrDataObj = json_decode($attrData->attribute_data);
                if(property_exists($attrDataObj,'name' )){
                    $name = $attrDataObj->name;
                }
                if(property_exists($attrDataObj,'site_title_line1' )){
                    $site_title_line1 = $attrDataObj->site_title_line1;
                }
                if(property_exists($attrDataObj,'site_title_line2' )){
                    $site_title_line2 = $attrDataObj->site_title_line2;
                }
            }

            $site = [
                'id' => $site->id,
                //'name' => Cache::get($host.'-'.$lang.'-name')!=null?Cache::get($host.'-'.$lang.'-name'):Cache::put($host.'-'.$lang.'-name',$site->getAttributeTranslated('name', $lang),time()+30*24*3600),
                'name' => $name,
                //'name' => $site->getAttributeTranslated('name', $lang),
                'default_lang' => $site->site_default_lang ?: 'bn',
                'languages' => $site->getSiteLanguages(),
                'logo' => $site->logo_url,
                'uuid' => $site->uuid,
                'db' => $site->db_id,
                'theme_code' => $site->getSiteTheme(),
                'layer_id' => $site->layer_id,
                'ministry_id' => $site->ministry_id,
                'geo_division_id' => $site->geo_division_id,
                'geo_district_id' => $site->geo_district_id,
                'geo_upazila_id' => $site->geo_upazila_id,
                'geo_union_id' => $site->geo_union_id,
                //'site_title_line1' => $site->getAttributeTranslated('site_title_line1', $lang),
                'site_title_line1' => $site_title_line1,
                //'site_title_line1' => Cache::get($host.'-'.$lang.'-site_title_line1')!=null?Cache::get($host.'-'.$lang.'-site_title_line1'):Cache::put($host.'-'.$lang.'-site_title_line1',$site->getAttributeTranslated('site_title_line1', $lang),time()+30*24*3600),
                'site_title_line2' => $site_title_line2,
                //'site_title_line2' => $site->getAttributeTranslated('site_title_line1', $lang),
               //'site_title_line2' => Cache::get($host.'-'.$lang.'-site_title_line2')!=null?Cache::get($host.'-'.$lang.'-site_title_line2'):Cache::put($host.'-'.$lang.'-site_title_line2',$site->getAttributeTranslated('site_title_line2', $lang),time()+30*24*3600),
                // 'updated_at' => $site->updated_at,
                'last_content_updated' => $site->last_content_updated,
                'slogan' => $site->slogan,
            ];

            //Session::put('site', $site);

            $key = 'database.connections.tenant';
            $db = config($key);
            $db['host'] = $cluster->host;
            $db['username'] = $cluster->username;
            $db['password'] = $cluster->password;
            $db['database'] = $site['db'];
            config(
                [$key => $db]
            );

            $request->merge([
                'site' => $site,
                'domain' => $host,
                'lang' => $lang,
                'isTenant' => 1,
                'ajax' => env('Ajax_END_POINT')
            ]);
        }
    }
}
