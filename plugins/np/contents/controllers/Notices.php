<?php

namespace Np\Contents\Controllers;

use Np\Structure\Controllers\NpBaseController;
use BackendMenu;
use Np\Contents\Models\Taxonomy;

class Notices extends NpBaseController
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'manage_notice'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents');
    }

    public function listExtendQuery()
    {
        // $list = Taxonomy::getReligiousInstitutesWithCount();
        // dump('okk');
    }
}
