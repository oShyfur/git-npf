<?php

namespace Np\Structure\Controllers;

use BackendMenu;
use Np\Structure\Controllers\NpBaseController;

class Ministry extends NpBaseController
{
    public $implement = ['Backend\Behaviors\ListController',        'Backend\Behaviors\FormController',        'Backend\Behaviors\ReorderController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = [
        'manage_ministry'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Structure', 'np-structure', 'np-ministries');
    }
}
