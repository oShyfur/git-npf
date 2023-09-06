<?php

namespace Np\Contents\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Np\Structure\Models\ContentType;
use Np\Structure\Classes\NP;
use Illuminate\Support\Facades\Log;
use RainLab\Translate\Models\Locale;
use Session;

/**
 * linkPicker Form Widget
 */
class LinkPicker extends FormWidgetBase
{
    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'linkpicker';

    /**
     * @var int Maximum rows to display for each page.
     */
    public $recordsPerPage = 10;

    /**
     * @var string Filters the relation using a raw where query statement.
     */
    public $conditions;

    /**
     * @var string Use a custom scope method for the list query.
     */
    public $scope;

    /**
     * @var string Use a custom modelName method for the list query.
     */
    public $modelName;

    /**
     * @var \Backend\Classes\WidgetBase Reference to the widget used for searching.
     */
    protected $searchWidget;

    protected $listWidget;

    protected $captionOldData;

    /**
     * @inheritDoc
     */
    public function init()
    {
        if (post('recordfinder_flag')) {

            $this->listWidget = $this->makeListWidget(post('recordfinder_flag'));

            $this->listWidget->addColumns([
                'action' => [
                    'label' => 'Action',
                    'sortable' => false,
                    'type' => 'partial',
                    'path' => '$/np/contents/formwidgets/linkpicker/partials/_action_column.htm'
                ]
            ]);

            $exploded = explode('/', post('recordfinder_flag'));
            $this->modelName = end($exploded);

            $this->searchWidget = $this->makeSearchWidget();
            $this->searchWidget->bindToController();

            $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());

            /*
             * Link the Search Widget to the List Widget
             */
            $this->searchWidget->bindEvent('search.submit', function () {
                $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());
                return $this->listWidget->onRefresh();
            });

            $this->listWidget->bindToController();

        }
        if (post('model')) {
            $this->listWidget = $this->makeListWidget(post('model'));
            $this->listWidget->addColumns([
                'action' => [
                    'label' => 'Action',
                    'sortable' => false,
                    'type' => 'partial',
                    'path' => '$/np/contents/formwidgets/linkpicker/partials/_action_column.htm'
                ]
            ]);

            $exploded = explode('/', post('model'));
            $this->modelName = end($exploded);
            $this->listWidget->bindToController();

            // $this->searchWidget = $this->makeSearchWidget();
            // $this->searchWidget->bindToController();

            // $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());

            // /*
            //  * Link the Search Widget to the List Widget
            //  */
            // $this->searchWidget->bindEvent('search.submit', function () {
            //     $this->listWidget->setSearchTerm($this->searchWidget->getActiveTerm());
            //     return $this->listWidget->onRefresh();
            // });
        } else
            $this->bindToController();
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('linkpicker');
    }

    /**
     * Prepares the form widget view data
     */

    public function getLocales()
    {
        return Locale::listAvailable();
    }
    public function prepareVars()
    {

        $this->vars['field'] = $this->formField;
        $this->vars['listWidget'] = $this->listWidget;
        $this->vars['name'] = $this->formField->getName();
        $this->vars['id'] = $this->getId();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
        $this->vars['locales'] = $this->getLocales();
        $this->vars['searchWidget'] = $this->makeSearchWidget();
        $this->vars['modelName']  = $this->modelName;
        //end(split('-',$str))
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        $this->addCss('css/linkpicker.css', 'np.contents');
        $this->addJs('js/linkpicker.js', 'np.contents');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        //traceLog($value);
        if (empty($value['link']))
            $value = '';

        return $value;
    }

    public function getLoadValue()
    {
        $value = $this->formField->value;

        if (empty($value)) {
            $value = [
                'link' => '',
                'caption' => [
                    'bn' => '',
                    'en' => ''
                ]
            ];
        } elseif (!is_array($value) and is_array(@unserialize($value))) {
            $data = unserialize($value);
            $item = $data[0];

            $value = [
                'link' => $item['link'],
                'caption' => [
                    'bn' => $item['caption_bn'],
                    'en' => $item['caption_en']
                ]
            ];
        }

        return $value;
    }

    //ajax callbacks
    public function onFindLink()
    {
        if(Session::has('old_caption_items')){
            Session::forget('old_caption_items');
        }
        $this->prepareVars();
        Session::put('old_caption_items', $this->formField->value);
        //var_dump($this->captionOldData );
        return $this->makePartial('linkfinder_form');
    }
    public function onFindContentTypes()
    {
        $this->prepareVars();
        $this->vars['list'] = session('site.resources.content_types') ?: [];
        return $this->makePartial('content_type_list');
    }

    public function onSelectContentType()
    {
        $this->prepareVars();
        $this->vars['value'] = str_replace('/', '\\', input('model'));

        //return ['#' . $this->getId('popop') => $this->makePartial('contents_list')];
        return $this->makePartial('contents_list');
    }

    public function onSelectContent()
    {
        //$this->prepareVars();
        $model = input('model');
        $slug = input('slug');
        $containerid = input('containerid');
        $id = input('id');
        $name = input('name');
        $containerid = input('containerid');
        //$link = NP::getDetailsUrl($model, $slug);
        $parts = explode('/', $model);
        $modelCode = $this->fromCamelCase(end($parts));
        $link =  '/site/' . $modelCode . '/' . $slug;

        $this->vars['name'] = $name;
        $this->vars['id'] = $id;
        $this->vars['locales'] = $this->getLocales();
        if(Session::has('old_caption_items')){
            $item =Session::get('old_caption_items') ;
        }
        $value = [
            'link' => $link,
            'caption' => [
                'bn' => isset($item['caption']['bn'])?$item['caption']['bn']:'',
                'en' => isset($item['caption']['en'])?$item['caption']['en']:''
            ]
        ];
        $this->vars['value'] = $value;

        return [
            'element' => $containerid,
            'value' => $this->makePartial('field')
        ];
    }

    private function fromCamelCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    public function onFindViews()
    {
        $this->prepareVars();
        $this->vars['views'] = session('site.resources.content_types') ?: [];
        return $this->makePartial('view_list');
    }

    public function onFindForms()
    {
        $this->prepareVars();
        $this->vars['forms'] = $this->findForms();
        return $this->makePartial('form_list');
    }
    public function onSelectForm()
    {
        $this->prepareVars();
        $link = NP::getFormUrl(input('form'));
        if(Session::has('old_caption_items')){
            $item =Session::get('old_caption_items') ;
        }
        $value = [
            'link' => $link,
            'caption' => [
                'bn' => isset($item['caption']['bn'])?$item['caption']['bn']:'',
                'en' => isset($item['caption']['en'])?$item['caption']['en']:''
            ]
        ];
        $this->vars['value'] = $value;
        return ['#' . $this->getId('container') => $this->makePartial('field')];
    }


    public function onSelectView()
    {
        $this->prepareVars();
        $link = NP::getViewUrl(input('view'));
        //$parts = explode('/', $model);
        $view_code = input('view');
        $parts = explode('-', $view_code);
        $view_code = isset($parts[1]) ? $parts[1] : $view_code;
        //$modelCode = $this->fromCamelCase(end($parts));
        //return '/site/view/' . $view_code;
        $link =  '/site/view/' . $this->fromCamelCase($view_code);
        if(Session::has('old_caption_items')){
            $item =Session::get('old_caption_items') ;
        }
        $value = [
            'link' => $link,
            'caption' => [
                'bn' => isset($item['caption']['bn'])?$item['caption']['bn']:'',
                'en' => isset($item['caption']['en'])?$item['caption']['en']:''
            ]
        ];
        $this->vars['value'] = $value;
        return ['#' . $this->getId('container') => $this->makePartial('field')];
    }

    //functions

    public function findForms()
    {
        return NP::findPartialsByPattern('form-%');
    }
    protected function makeListWidget($modelClass)
    {
        $model = strtolower($modelClass);
        $listConfig = '$/' . $model . '/columns.yaml';
        $config = $this->makeConfig($listConfig);
        $modelClassName = str_replace('/', '\\', $modelClass);
        $config->model = new $modelClassName;
        $config->alias = $this->alias . 'List';
        $config->showSetup = false;
        $config->showCheckboxes = false;
        $config->recordsPerPage = $this->recordsPerPage;
        $config->recordOnClick = sprintf("makePopup(this,'%s','%s','%s','%s')", $modelClass, $this->getId('container'), $this->getId(), $this->formField->getName());
        $widget = $this->makeWidget('Backend\Widgets\Lists', $config);
        $widget->bindToController();

        /*
         * Link the Search Widget to the List Widget
         */
/*
        if ($searchWidget = $this->makeSearchWidget()) {

            $searchWidget->bindEvent('search.submit', function () use ($widget, $searchWidget) {
                $widget->setSearchTerm($searchWidget->getActiveTerm());  // here it's the search term to which I need access
                $widget->setSearchOptions([
                    'mode' => $this->searchMode,
                    'scope' => $this->searchScope,
                ]);
                //$this->searchWidget = $this->makeSearchWidget();
                //$this->searchWidget->bindToController();
                $widget->bindToController();
                //return $widget->onRefresh();
            });

            // Find predefined search term
            $widget->setSearchTerm($searchWidget->getActiveTerm());

            if ($sqlConditions = $this->conditions) {
                $widget->bindEvent('list.extendQueryBefore', function ($query) use ($sqlConditions) {
                    $query->whereRaw($sqlConditions);
                });
            } elseif ($scopeMethod = $this->scope) {
                $widget->bindEvent('list.extendQueryBefore', function ($query) use ($scopeMethod) {
                    $query->$scopeMethod($this->model);
                });
            }


        }

        // $widget->setSearchOptions([
        //     'mode' => $this->searchMode,
        //     'scope' => $this->searchScope,
        // ]);

*/

        return $widget;
    }

    protected function makeSearchWidget()
    {
        $config = $this->makeConfig();
        $config->alias = $this->alias . 'Search';
        $config->growable = false;
        $config->prompt = 'backend::lang.list.search_prompt';
        $widget = $this->makeWidget('Backend\Widgets\Search', $config);
        $widget->cssClasses[] = 'recordfinder-search';
        return $widget;
    }
}
