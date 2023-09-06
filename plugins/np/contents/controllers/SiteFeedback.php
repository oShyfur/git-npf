<?php

namespace Np\Contents\Controllers;

use Backend;
use Backend\Classes\Controller;
use BackendMenu;
use Np\Contents\Models\SiteFeedback as ModelsSiteFeedback;
use Redirect;

class SiteFeedback extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];

    public $listConfig = 'config_list.yaml';

    public function __construct()
    {

        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents');

        $this->middleware(function ($request, $response) {
            return Redirect::back();
        });
    }

    public function index()
    {
        if (!in_array(input('code'), $this->siteForms()))
            return Redirect::to(Backend::url('np/contents/forms'));

        $this->asExtension('ListController')->index();
    }


    public function onViewFeedback()
    {
        $feedback = ModelsSiteFeedback::findOrFail(post('record_id'));
        return $this->makePartial('view_feedback', ['feedback' => $feedback]);
    }

    public function listExtendQuery($query, $definition = null)
    {
        $code = input('code');
        $query->where('form_id', $code);
    }

    public function siteForms()
    {

        $forms = session('site.resources.forms', []);
        return array_column($forms, 'code');
    }
}
