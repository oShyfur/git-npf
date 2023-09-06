<?php

namespace Np\Contents\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Np\Contents\Models\SiteFeedback;
use Np\Structure\Controllers\NpBaseController;

/**
 * Forms Back-end Controller
 */
class Forms extends NpBaseController
{


    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents-forms');
    }

    public function index()
    {
        $this->pageTitle = 'Forms';

        $data = [];

        $forms = session('site.resources.forms', []);

        foreach ($forms as $form) {
            $data[] = [
                'code' => $form['code'],
                'name' => $form['name'],
                'submissions' => SiteFeedback::countsSumbissions($form['code'])->first()
            ];
        }


        $this->vars['data'] = $data;
    }
}
