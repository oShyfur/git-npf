<?php
namespace Np\Contents\Classes;

use Np\Structure\Controllers\NpBaseController;
use Np\Contents\Models\NPContentsBaseModel;

class ContentModelExtension
{

    public function boot()
    {
        $this->extendModelList();
    }

    public function extendModelList()
    {
        NpBaseController::extendListColumns(function ($list, $model) {
            if (!$model instanceof NPContentsBaseModel) {
                return;
            }

            $list->addColumns([
                'my_column' => [
                    'label' => 'My Column'
                ]
            ]);
        });
    }
}
