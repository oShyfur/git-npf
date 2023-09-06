<?php

namespace Np\Contents\Controllers;

use Np\Structure\Controllers\NpBaseController;
use BackendMenu;

class News extends NpBaseController
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents');
    }
}
