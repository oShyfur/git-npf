<?php namespace Np\Contents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Np\Contents\Models\SiteBlocks;
use October\Rain\Support\Facades\Flash;
use Backend\Facades\Backend;

class SiteBlock extends Controller
{
    public $implement = ['Backend\Behaviors\FormController'];

    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'manage_blocks'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents-blocks');
    }
    public function index()
    {
        // add assets

        // custom css
        $this->addCss("/plugins/np/contents/assets/css/np_layout.css", "1.0.0");
        // custom js
        $this->addJs("//code.jquery.com/ui/1.12.1/jquery-ui.min.js", "1.0.0");
        $this->addJs("/plugins/np/contents/assets/js/np_layout.js", "1.0.0");
        $this->addJs("//cdn.jsdelivr.net/npm/sweetalert2@8.14.0/dist/sweetalert2.all.min.js", "1.0.0");

        $this->pageTitle = 'Blocks';
        $regions = SiteBlocks::allRegion();
        $data = [];
        $blocks = SiteBlocks::orderBy('sort_order', 'asc')->get();

        foreach ($blocks as $block) {
            $key = $block->region;
            $data[$key][] = $block;
        }
        $regions = array_merge($regions, $data);
        $this->vars['regions'] = $regions;
    }

    public function onSaveReorder()
    {

        $postedData = post('blocks');
        foreach ($postedData as $region => $blocks) {
            if (count($blocks)) {
                foreach ($blocks as $order => $blockId) {

                    $id = (int)$blockId;
                    $data = [
                        'region' => $region,
                        'sort_order' => $order
                    ];

                    SiteBlocks::find($id)->update($data);
                }
            }
        }
        Flash::success('Blocks updated successfully!');
        return redirect(Backend::url('np/contents/siteblock'));
    }
}
