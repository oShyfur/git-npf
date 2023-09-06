<?php

namespace Np\Structure\Behaviors;

use Illuminate\Support\Facades\Cache;
use System\Classes\ModelBehavior;

class CacheableModel extends ModelBehavior
{
    public function __construct($model)
    {
        $this->model = $model;

        $model::addGlobalScope('cacheScope', function ($builder) {

            $tags = self::getCacheTags($builder->getModel());

            $builder->rememberForever()->cacheTags($tags);
        });

        $this->model->bindEvent('model.afterSave', [$this, 'clearCacheTag']);
        $this->model->bindEvent('model.afterDelete', [$this, 'clearCacheTag']);
    }

    public function clearCacheTag()
    {
        $model = $this->model;
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
