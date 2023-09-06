<?php namespace Np\Contents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class TaxonomyType extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];

    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents-taxonomy');
    }

    public function listExtendQuery($query, $definition = null)
    {
        $ids = session('site.resources.taxonomy_types', []);
        $taxonomies_ids = array_column($ids, 'id');
        $query->whereIn('id', $taxonomies_ids);
    }
}
