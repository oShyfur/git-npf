<?php

namespace Np\Structure\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Np\Structure\Models\Site as NpSite;

class Site extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'manage_sites'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Structure', 'np-structure', 'np-sites');
    }

    public function listExtendQuery($query)
    { }
}
