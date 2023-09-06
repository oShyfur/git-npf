<?php

namespace Np\Structure\Classes;

use Backend\Models\User;
use RainLab\Translate\Models\Locale;

/**
 * Class BackendUserExtension
 * @package Renatio\Logout\Classes
 */
class TranslationExtension
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->enableCachingToModels();
    }
    /**
     * @return void
     */
    
    public function enableCachingToModels()
    {
        Locale::extend(function ($model) {
            $model->implement[] = 'Np.Structure.Behaviors.CacheableModel';
        });

       
    }

}
