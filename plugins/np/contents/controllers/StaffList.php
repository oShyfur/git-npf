<?php

namespace Np\Contents\Controllers;

use BackendMenu;
use Np\Contents\Models\StaffList as NpStaffList;
use Np\Structure\Controllers\NpBaseController;

class StaffList extends NpBaseController
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents');
    }

    public function getPublishItemsCount()
    {
        return $this->widget->list->model::where('publish', 1)->get()->count();
    }

    public function listExtendQuery($query)
    {
        if (isset($this->widget->list))
            $this->widget->list->recordsPerPage = $this->getPublishItemsCount();

        return $query->orderBy('publish', 'desc')->orderBy('sort_order', 'asc');
    }

    public function reorderExtendQuery($query)
    {
        return $query->where('publish', 1);
    }
}
