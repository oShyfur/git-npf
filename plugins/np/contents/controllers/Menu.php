<?php

namespace Np\Contents\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Np\Contents\Models\Menu as NpMenu;
use Np\Structure\Classes\NP;

/**
 * Menu Back-end Controller
 */
class Menu extends Controller
{

    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents-menu');
        parent::create();
    }

    public function formExtendFields($form, $fields)
    {

        $id = post('id');
        $action = post('action');

        if ($action == 'add' and $id) {
            $parent = $form->getField('parent');
            $parent->value = $id;
        }

        if ($form->context == 'update') {

            $link_path = $form->getField('link_path');

            $link_path->value = [

                'link' => $link_path->value,
                'caption' => [
                    'bn' => '',
                    'en' => ''
                ]
            ];
        }
    }


    public function onGetMenuForm()
    {
        parent::create();
        $this->vars['formRender'] = $this->formRender();

        return [
            '#menuForm' => $this->makePartial('form')
        ];
    }

    public function onMenuEdit()
    {
        $recordId = post('id');
        parent::update($recordId);

        $this->vars['formRender'] = $this->formRender();

        return [
            '#menuForm' => $this->makePartial('form')
        ];
    }


    public function onMenuAdd()
    {

        parent::create();

        $this->vars['formRender'] = $this->formRender();

        return [
            '#menuForm' => $this->makePartial('form')
        ];
    }

    public function onMenuSave()
    {
        $defaultLang = NP::getSite('default_lang');
        $localizeTitles  = post("RLTranslate");

        $data = post('Menu');
        $parent = $data['parent'] ? NpMenu::find($data['parent']) : null;
        $id = isset($data['id']) ? $data['id'] : null;


        $menu = $id ? NpMenu::find($id) : new NpMenu;
        $menu->title = $localizeTitles[$defaultLang]['title'];
        $menu->parent = $parent;
        $menu->link_type = $data['link_type'];
        $menu->link_path = $data['link_path']['link'];
	    $menu->menu_text_color = $data['menu_text_color'] ;
        $menu->menu_background_color = $data['menu_background_color'] ;
        $menu->default_text_color = $data['default_text_color'] ;
        $menu->default_background_color = $data['default_background_color'] ;

        if ($menu->link_type == 'nolink') {
            $menu->link_path = '';
        }

        $menu->depth = $parent ? (int) $parent->depth + 1 : 1;
        $menu->sort_order = $data['sort_order'];
        $menu->status = $data['status'];


        $menu->save();

        \Flash::success('Menu Saved!');

        return $this->listRefresh();

        //return $menu;
    }



    public function onMenuReset() {
        
        $this->vars['formRender'] = $this->formRender();
        // \Flash::success('Form Reset !');
        return [
            '#menuForm' => $this->makePartial('form')
        ];      
    }
}
