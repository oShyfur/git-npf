<?php

namespace Np\Contents\Traits;

use Illuminate\Support\Facades\Log;
use Np\Contents\Scopes\SiteScope;
use Np\Structure\Classes\NP;

trait SiteContentsTraitWithoutScope
{

    public static function bootSiteContentsTraitWithoutScope()
    {

        //static::addGlobalScope(new SiteScope);
        static::extend(function ($model) {

            $model->bindEvent('model.beforeCreate', function () use ($model) {
                $model->beforeCreateSiteContentsTrait();
                traceLog(get_class($model));
            });
        });
    }

    public function beforeCreateSiteContentsTrait()
    {
        $this->site_id = NP::getSiteId();
    }
}
