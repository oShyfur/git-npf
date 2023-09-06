<?php namespace Np\Structure\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Domain extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'manage_domain'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Structure', 'np-structure', 'np-domains');
    }
}
