<?php

namespace Np\Contents\Traits;

use Illuminate\Support\Facades\Log;
use Np\Structure\Classes\NP;
use Illuminate\Support\Facades\Cache;

trait Auditable
{

    public static function bootAuditable()
    {

        static::extend(function ($model) {

            $model->bindEvent('model.beforeCreate', function () use ($model) {
                $model->beforeContentCreate();
            });
            $model->bindEvent('model.beforeUpdate', function () use ($model) {
                $model->beforeContentUpdate($model);
            });

            // $model->bindEvent('model.beforeDelete', function () use ($model) {
            //     $model->beforeContentDelete();
            // });
            $model->bindEvent('model.afterDelete', function () use ($model) {
                $model->afterContentDelete();
            });
        });
    }

    public function beforeContentCreate()
    {
        $this->created_by = NP::getUserId();
    }

    public function beforeContentUpdate($model)
    {

        //Log::info($model->cacheTags);
        $this->updated_by =  NP::getUserId();
        Cache::tags($model->cacheTags)->flush();
    }

    // public function beforeContentDelete()
    // {
    //     $this->deleted_by =  NP::getUserId();
    //     $this->forceSave();
    // }

    public function afterContentDelete()
    {
        $this->deleted_by =  NP::getUserId();
        $this->forceSave();
        $this->revision_history()->delete();
    }
}
