<?php

namespace Np\Structure\Traits;

use Illuminate\Support\Facades\Log;
use Np\Structure\Classes\NP;
use Illuminate\Support\Facades\Cache;
use Np\Structure\Scopes\CacheScope;

trait Cacheable
{

    public static function bootCacheable()
    {

        static::addGlobalScope('cacheScope', function ($builder) {

            $tags = self::getCacheTags($builder->getModel());

            $builder->rememberForever()->cacheTags($tags);
        });
        static::extend(function ($model) {

            $model->bindEvent('model.afterSave', function () use ($model) {
                $model->clearCacheTag($model);
            });
            $model->bindEvent('model.afterDelete', function () use ($model) {
                $model->clearCacheTag($model);
            });
        });
    }

    public function clearCacheTag($model)
    {
        $tags = self::getCacheTags($model);

        Cache::tags($tags)->flush();
    }


    public static function getCacheTags($model)
    {
        $tags = [];
        $modelTag  = str_replace('\\', '_', strtolower(get_class($model)));
        if (self::isTenantData($model)) {
            $siteTag = '_site_' . NP::getSiteId();
            $modelTag .= $siteTag;
            $tags[] = $siteTag;
        }
        $tags[] = $modelTag;
        return $tags;
    }

    public static function isTenantData($class)
    {
        $traits = array_merge(
            class_uses($class),
            class_uses(get_parent_class($class))
        );

        return isset($traits['Np\Contents\Traits\SiteContentsTrait']);
    }
}
