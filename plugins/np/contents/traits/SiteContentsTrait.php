<?php

namespace Np\Contents\Traits;

use Illuminate\Support\Facades\Log;
use Np\Contents\Scopes\SiteScope;
use Np\Structure\Classes\NP;

trait SiteContentsTrait
{

    public static function bootSiteContentsTrait()
    {

        static::addGlobalScope(new SiteScope);
        static::extend(function ($model) {

            //relation to site
            $model->belongsTo['site'] = ['Np\Structure\Models\Site'];

            $model->bindEvent('model.beforeCreate', function () use ($model) {
                $model->beforeCreateSiteContentsTrait();
            });

            $model->bindEvent('model.afterSave', function () use ($model) {
                $model->updateSiteData();
            });
        });
    }

    public function beforeCreateSiteContentsTrait()
    {
        $this->site_id = $this->site_id ?: NP::getSiteId();
    }
    public function updateSiteData()
    {
        $site = $this->site;
        $site->last_content_updated = now();
        $site->save();
    }
}
