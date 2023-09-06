<?php

namespace Np\Contents\Controllers;

use BackendMenu;
use Np\Contents\Models\EducationInstitute as ModelsEducationInstitute;
use Np\Structure\Controllers\NpBaseController;

class EducationInstitute extends NpBaseController
{
    public $implement = ['Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents');
    }
}
