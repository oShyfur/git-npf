<?php namespace Np\Contents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Backend\Helpers\Backend;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Np\Contents\Scopes\SiteScope;

class Taxonomy extends Controller
{
    public $implement = ['Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'manage_taxonomy'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents-taxonomy');
    }

    public function listExtendQuery($query)
    {
        if (input('type')) {
            $typeId = input('type');
            $query->where('texonomy_type_id', $typeId);
			$query->withoutGlobalScope(SiteScope::class);
        }
    }

    public function formExtendFields($host, $fields)
    {

        foreach ($fields as $field) {
            if ($field->fieldName == 'texonomy_type_id' && !$field->value)
                $field->value = input('type');
        }
    }

    public function formAfterSave($model)
    { }

    public function update_onSave($context = null)
    {
        parent::update_onSave($context);
        $redirectUrl = 'np/contents/taxonomy?type=' . input('Taxonomy.texonomy_type_id');
        return \Backend::redirect($redirectUrl);
    }

    public function create_onSave($context = null)
    {
        parent::create_onSave($context);
        $redirectUrl = 'np/contents/taxonomy?type=' . input('Taxonomy.texonomy_type_id');
        return \Backend::redirect($redirectUrl);
    }
}
