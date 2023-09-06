<?php

namespace Np\Structure\Classes;

use System\Models\File;

class SystemFileExtension
{

    public function boot()
    {
        $this->extendBackendFileModel();
    }

    public function extendBackendFileModel()
    {
        File::extend(function ($model) {
            $model->implement[] = 'Np.Structure.Behaviors.CacheableModel';
        });
    }
}
