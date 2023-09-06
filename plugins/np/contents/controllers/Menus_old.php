<?php namespace Np\Contents\Controllers;

use Illuminate\Http\Request;
use Backend\Classes\Controller;
use Backend\Facades\BackendMenu;
use October\Rain\Support\Facades\Flash;
use Illuminate\Support\Facades\Validator;
use October\Rain\Exception\ValidationException;
use Np\Structure\Classes\NP;

// models
use Np\Contents\Models\Menu;

class Menus extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];

    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents');
        // custom css
        $this->addCss("/plugins/np/contents/assets/css/nestable.css", "1.0.0");
        $this->addCss("/plugins/np/contents/assets/css/np_menu.css", "1.0.0");
        // custom js
        $this->addJs("/plugins/np/contents/assets/js/nestable.js", "1.0.0");
        $this->addJs("/plugins/np/contents/assets/js/np_menu.js", "1.0.0");
//        $this->addJs("https://cdn.jsdelivr.net/npm/sweetalert2@8.14.0/dist/sweetalert2.all.min.js", "1.0.0");
    }

    public function onGetMenu()
    {
        $site_id = NP::getSiteId();
        $menus = Menu::where('site_id', $site_id)->where('status', 1)->orderBy('weight', 'asc')->get();
    
        return [
            'menus' => $menus
        ];
    }

    public function onGetHierarchicalMenu()
    {
        $site_id = NP::getSiteId();
        $menus = Menu::with('children')->where('site_id', $site_id)->where('parent_id', '0')->where('status', 1)->orderBy('weight', 'asc')->get();
    
        return [
            'menus' => $menus
        ];
    }

    public function onSubmitMenuForm()
    {
        // post request data
        $data = post();

        // validation rules
        $rules = [
            'title' => 'required|array|min:2',
            "title.*" => "required|min:2",
            'link_type' => 'required',
            'weight' => 'required|numeric'
        ];

        // validating request data
        $validation = Validator::make($data, $rules);

        // checking if validation fails
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
    
        // site id
        $site_id = NP::getSiteId();
        
        // parent id
        $parent_id = $data['parent_id'] != null ? $data['parent_id'] : 0;
        
        $data['RLTranslate'] = $data['title'];

        // storing menu data
        $menu = new Menu();
        $menu->title = $data['title']['bn'];
        $menu->translateContext('en');
        $menu->title = $data['title']['en'];
        $menu->parent_id = $parent_id;
        $menu->depth = $this->getDepth($parent_id);
        $menu->link_type = $data['link_type'];
        if ($data['link_type'] != 3) {
            $menu->link_path = $data['link_path'];
        }
        $menu->weight = $data['weight'];
        $menu->status = $data['status'];
        $menu->site_id = $site_id;
        $menu->save();

        // showing success message
        Flash::success('Menu added successfully!');
    }
    
    private function getDepth($parent_id)
    {
        if ($parent_id) {
            $menu = Menu::find($parent_id);
            if (!empty($menu)) {
                return $menu->depth + 1;
            }
        }
        return 1;
    }

    public function onGetSingleMenu()
    {
        // post data
        $data = post();

        // find meny using id
        $menu = Menu::findOrFail($data['id']);

        // returning data
        return [
            'menu' => $menu
        ];
    }

    public function onEditMenuForm()
    {
        // post request data
        $data = post();

        // validation rules
        $rules = [
            'title' => 'required|array|min:2',
            "title.*" => "required|min:2",
            'link_type' => 'required',
            'weight' => 'required|numeric'
        ];

        // validating request data
        $validation = Validator::make($data, $rules);

        // checking if validation fails
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        // updating menu data
        $menu = Menu::findOrFail($data['id']);
        $menu->title = json_encode($data['title']);
        $menu->link_type = $data['link_type'];
        if ($data['link_type'] != 3) {
            $menu->link_path = $data['link_path'];
        }
        $menu->weight = $data['weight'];
        $menu->status = $data['status'];
        $menu->update();

        // showing success message
        Flash::success('Menu updated successfully!');
    }

    public function onDeleteMenu()
    {
        $data = post();

        // find meny using id
        $menu = Menu::findOrFail($data['id']);

        // deleting menu
        if ($menu->delete()) {
            $message = "Menu deleted successfully";
        } else {
            $message = "Menu cannot be deleted";
        }

        // showing success message
        Flash::success($message);
    }

    public function onUpdateParentChild()
    {
        // post data
        $data = post();
        // list
        $list = json_decode($data['arr']);
        // weight level 1
        $weight1 = 1;
        // updating menu parent
        foreach ($list as $item1) {
            // menu parent update
            $menu1 = Menu::findOrFail($item1->id);
            $menu1->parent_id = 0;
            $menu1->weight = $weight1++;
            $menu1->update();
            // weight level 2
            $weight2 = 1;
            // checking for if child exists
            if (isset($item1->children)) {
                $list = [];
                $list = $item1->children;
                // updating menu parent
                foreach ($list as $item2) {
                    // menu parent update
                    $menu2 = Menu::findOrFail($item2->id);
                    $menu2->parent_id = $menu1->id;
                    $menu2->weight = $weight2++;
                    $menu2->update();
                    // weight level 3
                    $weight3 = 1;
                    // checking for if child exists
                    if (isset($item2->children)) {
                        $list = [];
                        $list = $item2->children;
                        // updating menu parent
                        foreach ($list as $item3) {
                            // menu parent update
                            $menu3 = Menu::findOrFail($item3->id);
                            $menu3->parent_id = $menu2->id;
                            $menu3->weight = $weight3++;
                            $menu3->update();
                        }
                    }
                }
            }
        }
    }
}
