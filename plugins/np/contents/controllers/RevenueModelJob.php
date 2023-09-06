<?php namespace Np\Contents\Controllers;

use Np\Structure\Controllers\NpBaseController;
use BackendMenu;

class RevenueModelJob extends NpBaseController
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController',        'Backend\Behaviors\ReorderController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents');
    }
}
