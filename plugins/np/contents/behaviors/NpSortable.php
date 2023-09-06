<?php

namespace Np\Contents\Behaviors;

use October\Rain\Database\Traits\Sortable;
use System\Classes\ModelBehavior;

class NpSortable extends ModelBehavior
{

    public function __construct($model)
    {
        $this->model = $model;

        $this->model->bindEvent('model.beforeCreate', [$this, 'beforeContentCreate']);
        $this->model->bindEvent('model.beforeUpdate', [$this, 'beforeContentUpdate']);
        $this->model->bindEvent('model.beforeDelete', [$this, 'beforeContentDelete']);
    }

    public function beforeContentCreate()
    {
        $maxOrder = self::where('site_id', $this->site_id)->where('publish', 1)->max('sort_order');
        $this->sort_order =  $maxOrder + 1;
    }

    public function beforeContentUpdate($model)
    {

        //set sort order to 0
        if (empty($this->publish) and $this->sort_order)
            $this->sort_order = 0;

        if ($this->publish and empty($this->sort_order)) {
            $maxOrder = self::where('site_id', $this->site_id)->where('publish', 1)->max('sort_order');
            $this->sort_order =  $maxOrder + 1;
        }
    }

    public function beforeContentDelete()
    {
        $this->sort_order = 0;
    }


    public function setSortableOrder($itemIds, $itemOrders = null)
    {
        $this->setSortableOrder($itemIds, $itemOrders);
        $this->clearCacheTag($this);
    }
}
