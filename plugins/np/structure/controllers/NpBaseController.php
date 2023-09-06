<?php

namespace Np\Structure\Controllers;

use Backend;
use Backend\Classes\Controller;
use Np\Contents\Models\NPContentsBaseModel;
use Redirect;
use Response;
use View;
//
use Backend\Facades\BackendAuth;
use Np\Structure\Classes\SiteSessionData;
use Illuminate\Support\Facades\Session;

class NpBaseController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function listExtendColumns($list)
    {
        $siteId = session('site.id');
        $loggedInUser = BackendAuth::getUser();
        $site = $loggedInUser->getSiteList()->firstWhere('id', $siteId);
        if ($site != null) {
            $contentsUpdateLists = SiteSessionData::contentLastUpdateGet($site);
            $currentCotent = array();
            $table = $list->model->getTable();
            foreach ($contentsUpdateLists as $ctl => $value) {
                if($table == $value['tableName']){
                    $currentCotent = $value;
                }
            }
            Session::put('currentCotentUpdateStatus', $currentCotent);
        }

        if ($list->model instanceof NPContentsBaseModel)
            $list->addColumns([
                'actions' => [
                    'label' => 'Actions',
                    'type' => 'partial',
                    'path' => '$/np/structure/layout/backend/_list_actions.htm',
                    'clickable' => false,
                    'sortable' => false
                ],

            ]);
    }
}
