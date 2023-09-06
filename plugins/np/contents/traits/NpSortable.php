<?php

namespace Np\Contents\Traits;

use Illuminate\Support\Facades\Log;
use Np\Structure\Classes\NP;
use Illuminate\Support\Facades\Cache;
use October\Rain\Database\Traits\Sortable;

trait NpSortable
{

    public static function bootNpSortable()
    {

        static::extend(function ($model) {

            $model->bindEvent('model.beforeCreate', function () use ($model) {
                $model->beforeContentCreate();
            });
            $model->bindEvent('model.beforeUpdate', function () use ($model) {
                $model->beforeContentUpdate($model);
            });

            $model->bindEvent('model.beforeDelete', function () use ($model) {
                $model->beforeContentDelete();
            });
        });
    }

    public function beforeContentCreate()
    {
        $maxOrder = self::where('site_id', $this->site_id)->where('publish', 1)->max($this->getSortOrderColumn());
        $this->{$this->getSortOrderColumn()} =  $maxOrder + 1;
    }

    public function beforeContentUpdate($model)
    {

        //set sort order to 0
        if (empty($this->publish) and $this->sort_order)
            $this->{$this->getSortOrderColumn()} = 0;

        if ($this->publish and empty($this->{$this->getSortOrderColumn()})) {
            $maxOrder = self::where('site_id', $this->site_id)->where('publish', 1)->max($this->getSortOrderColumn());
            $this->{$this->getSortOrderColumn()} =  $maxOrder + 1;
        }
    }

    public function beforeContentDelete()
    {
        $this->{$this->getSortOrderColumn()} = 0;
    }
}
