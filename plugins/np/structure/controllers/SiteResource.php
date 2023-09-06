<?php namespace Np\Structure\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class SiteResource extends Controller
{
    public $implement = ['Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Structure', 'np-structure', 'np-structure-site-resources');
    }
}
