<?php

namespace Np\Structure\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Np\Structure\Classes\SiteSessionData;
use Illuminate\Support\Facades\DB;
use Np\Structure\Models\Domain;
use Np\Structure\Models\Site;

class SiteSelect extends ReportWidgetBase
{
    protected $defaultAlias = 'siteSelect';
    public function render()
    {

        $user  = BackendAuth::getUser();
        $sites = BackendAuth::getUser()->getSiteList();
        return $this->makePartial('sites', ['sites' => $sites, 'user' => $user]);
    }

    public function onCacheClear()
    {
        Artisan::call('cache:clear');
        \Flash::success('Cache has been cleared');
    }

    public function onCacheClearSelectedSite()
    {
        $siteId = input('current_site');
        $domainData = DB::table('np_structure_domains')->select('fqdn')->where('site_id', $siteId)->get();
        if(!empty($domainData)){
            foreach ($domainData as $row){
                $domain = $row->fqdn;
                $domain = ltrim($domain, 'www.');
                try {
                    $keys = Redis::get($domain);
                    if (!empty($keys)) {
                        $keys = json_decode($keys);
                        foreach ($keys as $val) {
                            Redis::del($val);
                        }
                    }
                    // Redis::del($domain);
                }catch(\Exception $exception){}
            }
        }
        $cacheTag = '_site_' . $siteId;
        Cache::tags($cacheTag)->flush();
        \Flash::success('Cache has been cleared');
    }


    public function onSiteSelect()
    {
        $siteId = input('current_site');
        $default = input('default');
        $loggedInUser = BackendAuth::getUser();
        $site = $loggedInUser->getSiteList()->firstWhere('id', $siteId);

        if ($site) {
            // update default site
            if ($default) {
                DB::table('np_structure_site_user')
                    ->where('user_id', $loggedInUser->id)
                    ->where('default', 1)
                    ->update([
                        'default' => 0
                    ]);

                DB::table('np_structure_site_user')
                    ->where('user_id', $loggedInUser->id)
                    ->where('site_id', $siteId)
                    ->update([
                        'default' => 1
                    ]);
                //$loggedInUser->sites()->attach($siteId, ['default' => 1]);
            }

            // update session
            SiteSessionData::setSite($site);
            $redirectUrl = config('cms.backendUri') . '/np/contents/contenttype';
            return redirect($redirectUrl);
        }
    }

    function onAjaxTest()
    {
        // update MySQL here
        echo "Called";
    }
}
