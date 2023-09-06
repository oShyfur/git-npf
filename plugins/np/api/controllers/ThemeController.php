<?php

namespace Np\Api\Controllers;
use DB;
use Log;
use View;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Np\Contents\Models\File;
use Np\Contents\Models\News;
use Np\Structure\Classes\NP;
use Np\Api\Twig\ThemeManager;
use Np\Structure\Models\Site;
use System\Models\MailPartial;
use Np\Contents\Models\Notices;
use Np\Structure\Models\Domain;
use System\Models\MailTemplate;
use Np\Contents\Models\Taxonomy;
use Illuminate\Http\UploadedFile;
use Np\Structure\Models\ContentType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use RainLab\Translate\Models\Attribute;
use Np\Api\Controllers\APIBaseController;

/**
 * Sample Resourceful Test Controller
 */
class ThemeController extends APIBaseController
{
    use \Backend\Traits\FormModelSaver;
    use \Backend\Traits\FormModelWidget;

    public $defaultTheme = 'theme-default';
    public $domain = null;
    public $lang = 'bn';


    public function __construct(Request $request)
    {
    }

    public function ajax()
    {
        return ['okkk'];
    }

    protected function getThemePage($page = 'home')
    {
        $theme = Input::get('site.theme_code');
        return $theme . '-page-' . $page;
    }

    protected function getFormCode($code)
    {
        $code = str_replace('-', '_', $code);
        $partial = 'form-' . $code . '-default';

        return $partial;
    }


    public function home(Request $request)
    {
//        $array =['success'=>true,'data'=>'Hello World', 'message'=> 'successful'];
//        return json_encode($array);
//        die();
        $page = $this->getThemePage('home');

        $data = [
            'lang' => $request->lang,
            'domain' => $request->domain,
            'protocol' => $request->protocol,
            'q' => Input::all(),
            'url' => $this->get_url($request),
            'body_css' => 'home',
            'request' => $request->toArray()
        ] + $request->toArray();

        try{
            $html = $this->renderTemplate($page, $data);
        }catch (\Exception $exception){}
        if($html == ""){
            $template = MailTemplate::findOrMakeTemplate('theme-default-page-error');
            $html =  ThemeManager::instance()->errorRenderTemplate($template, $data);
        }
        return $this->sendResponse($html, 'successful');

    }

    public function form(Request $request, $form_code)
    {
        $page = $this->getThemePage('form');

        $data = [
            'lang' => $request->lang,
            'form_partial' => $this->getFormCode($form_code),
            'form_code' => $form_code,
            'domain' => $request->domain,
            'protocol' => $request->protocol,
            'q' => Input::all(),
            'url' => $this->get_url($request),
            'body_css' => 'form',
            'request' => $request->toArray()
        ] + $request->toArray();

        $html = $this->renderTemplate($page, $data);
        return $this->sendResponse($html, 'successful');
    }


    public function list(Request $request, $view_code)
    {
        $page = $this->getThemePage('list');
        try{
            $is_archive = false; //strpos($view_code, '_archive') !== false;
            //$view_code = str_replace('_archive', '', $view_code);
            $_view_code = $view_code;
            $view_code = $_view_code = $this->camelize($view_code);

            $content_type = ContentType::where('code', $view_code)->first();
            $data = [
                'lang' => $request->lang,
                'code' => $view_code,
                '_code' => $view_code,
                'domain' => $request->domain,
                'is_archive' => $is_archive,
                'protocol' => $request->protocol,
                'q' => Input::all(),
                'url' => $this->get_url($request),
                'body_css' => 'list',
                'request' => $request->toArray()
            ];


            $config = $this->get_config($content_type, 'list');
            if (isset($config)) {
                $list_data = $this->prepare_list_data($request, $content_type, $config, $is_archive);
                $data = array_merge($data, $list_data);
            } else {
                $data['view_code'] = NP::getViewCode('list', $_view_code);
            }


            $html = $this->renderTemplate($page, $data);
            return $this->sendResponse($html, 'successful');
        } catch(\Exception $exception) {
            //return $exception;
            //$page = $this->getThemePage('list');
            $data['view_code'] = 'details-error-default';
            $html = $this->renderTemplate($page, $data);
            return $this->sendResponse($html, 'successful');
        }
    }

    public function dynamicEmployeeList(Request $request, $view_code,$slug)
    {
        //dd($request);
        $page = $this->getThemePage('list');
        $is_archive = false; //strpos($view_code, '_archive') !== false;
        //$view_code = str_replace('_archive', '', $view_code);
        $_view_code = $view_code;
        $view_code = $_view_code = $this->camelize($view_code);

        $content_type = ContentType::where('code', $view_code)->first();

        $data = [
            'lang' => $request->lang,
            'code' => $view_code,
            '_code' => $view_code,
            'domain' => $request->domain,
            'is_archive' => $is_archive,
            'protocol' => $request->protocol,
            'q' => Input::all(),
            'url' => $this->get_url($request),
            'body_css' => 'list',
            'request' => $request->toArray()
        ];


        $config = $this->get_config($content_type, 'list');
        //dd($content_type);
        $dynamicSearch = [];
//        $searchable = $searchConfig['columns'];
//        $mode  = isset($searchConfig['mode']) ? $searchConfig['mode'] : 'all';
//        $items = call_user_func_array(array($items, 'searchWhere'), [$qs['q'], $searchable, $mode]);

        if (isset($config)) {
            $dynamicSearch['columns'] = array('designation');
            $dynamicSearch['q'] = $slug;

            $list_data = $this->prepare_list_data($request, $content_type, $config, $is_archive, $dynamicSearch);
            $data = array_merge($data, $list_data);
        } else {
            $data['view_code'] = NP::getViewCode('list', $_view_code);
        }


        $html = $this->renderTemplate($page, $data);
        return $this->sendResponse($html, 'successful');
    }
    
    public function details(Request $request, $view_code, $slug)
    {

        $lang = $request->lang;
        $page = $this->getThemePage('details');
        try{
            $_view_code = $view_code;
            $view_code = $this->camelize($view_code);
            $content_type = ContentType::where('code', $view_code)->first();

            //dd($content_type);

            $data = [
                'view_code' => NP::getViewCode('details', $_view_code),
                '_code' => $_view_code,
                'code' => $view_code,
                'lang' => $request->lang,
                'slug' => $slug,
                'domain' => $request->domain,
                'protocol' => $request->protocol,
                'q' => Input::all(),
                'url' => $this->get_url($request),
                'body_css' => 'details',
                'request' => $request->toArray()
            ];

            // $config = $this->get_config($content_type, 'details');
            // $listTitle = $this->get_config($content_type, 'list');

            // if (isset($config)) {
            //     //$details_data = $this->prepare_details_data($request, $content_type, $config, $slug);
            //     //$data = array_merge($data, $details_data);
            //     $data['view_code'] = 'details-common-default';
            //     $item = $content_type->table_name::where('slug', $slug)->where('publish', 1)->first();
            //     if ($item) {
            //         $data['record'] = [
            //             'item' => $item,
            //             'config' => $config,
            //             "title" => isset($listTitle['title']) ? $listTitle['title'] : ''
            //         ];
            //     }
            // }


            $config = $this->get_config($content_type, 'details');

            $right_sidebar_controll = $this->get_config($content_type, 'right_sidebar_controll');
            $listTitle = $this->get_config($content_type, 'list');


            if (isset($config) || isset($right_sidebar_controll)) {
                //$details_data = $this->prepare_details_data($request, $content_type, $config, $slug);
                //$data = array_merge($data, $details_data);
                $item = $content_type->table_name::where('slug', $slug)->where('publish', 1)->first();
            // dd($item);
                foreach ($item as $k => $v) {
                    foreach ($config['columns'] as $k1 => $v1) {

                        $field_name = $v1['name'];
                        $value = $item->{$field_name};
                        $type = $v1['type'];
                        if ($type == 'taxonomy' && $value != "" && !empty($value)) {
                        
                            if (is_array($value))
                            {
                                //var_dump($value);
                                // var_dump($field_name);
                                // $texonomoyValuArray = json_decode($value);
                                $texonomoyValuArray = $value;
                                if (!empty($texonomoyValuArray) &&
                                    count($texonomoyValuArray) > 0
                                ) {
                                
                                    $texonomyValue = [];
                                    foreach ($texonomoyValuArray as $singleTexonomy) {
                                        $texonomyArray = Taxonomy::where('id', $singleTexonomy)->first();

                                        if ($texonomyArray != "") {
                                            // $texonomyValue[]= $texonomyArray->name;
                                            $texonomyValue[] = $texonomyArray->getAttributeTranslated('name', $lang);
                                        }
                                    }
                                    $item->{$field_name}  = $texonomyValue;                                
                                }
                            }
                        // break;
                        }

                    }
                    break;
                }
    //dd($item);
                $data['item'] = $item;
                if ($item) {
                    if (!isset($config)) {

                        $data['record'] = [
                            'item' => $item,
                            // 'config' => $config,
                            // "title" => isset($listTitle['title']) ? $listTitle['title'] : ''
                        ];
                    } else {
                        $data['view_code'] = 'details-common-default';
                        $data['record'] = [
                            'item' => $item,
                            'config' => $config,
                            'content_type' => $content_type,
                            "title" => isset($listTitle['title']) ? $listTitle['title'] : ''
                        ];
                    }

                }
            }
    //die();
            //trace_log($data);
            $html = $this->renderTemplate($page, $data);
            return $this->sendResponse($html, 'successful');
        } catch(\Exception $exception) {
            //return $exception;
            //$page = $this->getThemePage('list');
            $data['view_code'] = 'details-error-default';
            $html = $this->renderTemplate($page, $data);
            return $this->sendResponse($html, 'successful');
        }
    }


    public function generateTopBar($currentSiteId)
    {
    }

    // helping functions
    public function variableForAllViews()
    {

        $cdn = env('CDN_PUBLIC_URL');
        $theme = Input::get('site.theme_code') ?: $this->defaultTheme;

        $global = [
            'base_cdn_url' => $cdn,
            'theme_path' => $cdn . '/media/central/themes/' . $theme,
            'theme' => $theme,
            'site_id' => Input::get('site.id'),
            'domain' => Input::get('domain')
        ];

        return $global;
    }

    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => ['html' => $result],
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    public function sendError($error, $errorMessages = [], $code = 404)
    {

        $response = [
            'success' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }

    public function renderTemplate($page, $data)
    {

        $data['global'] = $this->variableForAllViews();
        $template = MailTemplate::findOrMakeTemplate($page);

        $html = ThemeManager::instance()->renderTemplate($template, $data);

        return $html;
    }

    private function get_config($content_types, $config)
    {
        if ($this->has_config($content_types)) {
            $json = json_decode($content_types->config, true);
            if (isset($json[$config])) {
                return $json[$config];
            }
        }
    }

    private function has_config($content_types)
    {
        if ($content_types && $content_types->config)
            return true;
    }

    public function getAllChildsAndSelf($siteArray, $layer = null)
    {
        # code...
        //$layerId = $site;
    }
    private function prepare_list_data($request, $content_types, $config, $is_archive, $dynamicSearch=[])
    {

        $qs = Input::all();
        $lang = $request->lang;
        $defaultLang = $request->default_lang;
        $layers = [];
        $is_aggregated = isset($config['aggregated']);
        $toolbar = isset($config['toolbar']) ? $config['toolbar'] : false;

        // content model
        $items = $content_types->table_name;


        //aggregation 
        if ($is_aggregated) {

            // remove site scope

            $siteId = $qs['site']['id'];
            $site = Site::find($siteId);
            $layers = $site->contextLayers();

            // at default, show only current site data otherwise show layerwise data
            if (isset($qs['layer_id']) and $requested_layer = (int) $qs['layer_id']) {


                //layer filtering
                $sites = Site::where('layer_id', $requested_layer);

                //geo filtering

                if ($geo_division = (int) $site->geo_division_id)
                    $sites->where('geo_division_id', $geo_division);

                if ($geo_district = (int) $site->geo_district_id)
                    $sites->where('geo_district_id', $geo_district);

                if ($geo_upazila = (int) $site->geo_upazila_id)
                    $sites->where('geo_upazila_id', $geo_upazila);

                if ($geo_union = (int) $site->geo_union_id)
                    $sites->where('geo_union_id', $geo_union);

                $sites->orderBy('id', 'desc');

                $siteIds = $sites->pluck('id');

                //remove site scope
                $items = call_user_func([$items, 'withoutGlobalScopes']);
                $items = call_user_func_array([$items, 'whereIn'], ['site_id', $siteIds]);
            }
        }


        // archieving
        // $archivingConfig = isset($config['archiving']) ? $config['archiving'] : true;
        // if ($archivingConfig) {
        //     if ($is_archive) {
        //         $items = call_user_func_array(array($items, 'where'), ['archive_date', '<', date("Y-m-d")]);
        //     } else {

        //         $items = call_user_func_array(array($items, 'where'), ['archive_date', '>=', date("Y-m-d")]);
        //         $items = call_user_func_array(array($items, 'orWhere'), ['archive_date', '=', null]);
        //     }
        // }

        //Taxonomy filter
        $filters = [];
        if (isset($toolbar['taxonomy_filter']) and is_array($toolbar['taxonomy_filter'])) {
            //
            $filterItems = $toolbar['taxonomy_filter'];
            //dd($filterItems);
            foreach ($filterItems as $item) {

                $name = $item['name'];
                $filter['name'] = $name;
                $filter['label'] = isset($item['displayName'][$lang]) ? $item['displayName'][$lang] : '';
                $filter['type'] = $item['type'];


                $fieldName = $item['name'] . '_taxonomy';
                $list = call_user_func_array(array($items, 'with'), [$fieldName]);
                $list = call_user_func(array($list, 'get'));

                // filter with query param
                if (isset($qs[$name]) and !empty($qs[$name]))
                    $items = call_user_func_array(array($items, 'where'), [$name, $qs[$name]]);


                $itemData = [];
                foreach ($list as $row) {
                    //
                    $taxonomy = $row->{$fieldName};
                    if ($taxonomy)
                        $itemData[] = [
                            'id' => $taxonomy->id,
                            'name' => $lang != 'bn' ? $taxonomy->lang($lang)->name : $taxonomy->name
                        ];
                }
                $itemData = array_column($itemData, 'name', 'id');
                //trace_log($itemData);

                $filter['data'] = $itemData;
                $filters[] = $filter;
            }
        }


        // dynamic sorting
        if (isset($config['sortOrder'])) {
            $orders = $config['sortOrder'];
            foreach ($orders as $order) {
                $name = $order['name'];
                $direction = $order['direction'] ?: 'asc';
                $items = call_user_func_array(array($items, 'orderBy'), [$name, $direction]);
            }
        } else
            $items = call_user_func_array(array($items, 'orderBy'), ['id', 'desc']);


        //where conditions
        $items = call_user_func_array(array($items, 'where'), ['publish', 1]);

        //search process query string if any
        $searchConfig = isset($toolbar['search']) ? $toolbar['search'] : false;
        if ($searchConfig and isset($qs['q'])) {
            $searchable = $searchConfig['columns'];
            $mode  = isset($searchConfig['mode']) ? $searchConfig['mode'] : 'all';
            $items = call_user_func_array(array($items, 'searchWhere'), [$qs['q'], $searchable, $mode]);
        }

        $data['view_code'] = 'list-common-default';
        if(isset($dynamicSearch) && !empty($dynamicSearch) && $dynamicSearch!=""){

            $items = call_user_func([$items, 'withoutGlobalScopes']);
            $items = call_user_func_array(array($items, 'where'), ['deleted_at', null]);
            //$items = call_user_func_array([$items, 'whereIn'], ['site_id', ['13']]);
            $qs = [];
            $searchable = $dynamicSearch['columns'];
            $qs['q'] = $dynamicSearch['q'];
            $mode  =  'exact';// dd($qs);
            $items = call_user_func_array(array($items, 'searchWhere'), [$qs['q'], $searchable, $mode]);
            $data['view_code'] = 'list-employee_list-default';
        }

        //pagination
        $perPgae = isset($config['perPage']) ? (int) $config['perPage'] : 20;
        $items = call_user_func_array(array($items, 'simplePaginate'), [$perPgae]);


        $list_items = [];
        $columns = $config['columns'];


        $current_page = $items->currentPage();
        $per_page = $items->perPage();
        //dd($items);
        if (isset($columns)) {
            $sl = 0;
            $sl += (($current_page * $per_page) - $per_page);
            foreach ($items as $k => $v) {

                $row = [];
                $sl++;
                foreach ($columns as $k1 => $v1) {

                    $conf = $v1;
                    $field_name = $v1['name'];
                    $type = $v1['type'];
                    $value = $v->{$field_name};

                    //dd($value);


                    if ($lang != $defaultLang && in_array($field_name, $v->translatable)) {
                        $value = $v->lang($lang)->{$field_name};
                    }
                    if($type != 'file' && $type != 'image' && $type != 'relation')
                    //$value = Cache::get($request->domain.'-prepare_list_data-'.$sl.$lang)!=null?Cache::get($request->domain.'-prepare_list_data-'.$sl.$lang):Cache::put($request->domain.'-prepare_list_data-'.$sl.$lang,$v->getAttributeTranslated($field_name,$lang),time()+30*24*3600);
                    $value = $v->getAttributeTranslated($field_name,$lang);

                    switch ($type) {
                        case 'text':
                            $value = stripslashes($value);
                            break;
                        case 'taxonomy':
                            
                            $texonomyValue = '';
                            if(is_array($value) && $value != "" && !empty($value))
                            {

                                // $texonomoyValuArray = json_decode($value);
                                $texonomoyValuArray = $value;
                                if(!empty($texonomoyValuArray) &&
                                    count($texonomoyValuArray)>0){
                                    //$value = '';
                                    foreach ($texonomoyValuArray as $singleTexonomy){
 
                                        $texonomyArray = Taxonomy::where('id', $singleTexonomy)->first();
                                        //dd($texonomyArray);
                                        if($texonomyArray!="" ){
                                            //$texonomyValue.= $texonomyArray->name. ', ';
                                            if(strlen($texonomyValue)<187){
                                                $texonomyValue.= $texonomyArray->getAttributeTranslated('name', $lang) . ', ';
                                                //var_dump($texonomyValue);
                                            }
                                        }

                                    }
                                    $value = rtrim($texonomyValue,', ');
                                }

                            }else{
                                //dd($value);
                                $fieldName = $field_name . "_taxonomy"; // texonomy ?!
                                //dd($v);
                                $taxonomy = $v->{$fieldName};
                                //var_dump($value);

                                // $value = $taxonomy ? $taxonomy->name : '';
                                $value = $taxonomy ? $taxonomy->getAttributeTranslated('name', $lang) : '';
                                
                            }

                           // var_dump($value);

                            //dd('NO...');
                            break;
                        case 'date':
                            if ($value != null) {
                                $value = date('d-m-Y', strtotime($value));
                                $value = $this->translateDigit($value, $lang);
                            }                            
                            break;
                        case 'datetime':
                            if ($value != null) {
                                $value = date('d-m-Y H:i:s', strtotime($value));
                                $value = $this->translateDigit($value, $lang);
                            }                            
                            break;
                        case 'relation':
                            $column = isset($v1['column']) ? $v1['column'] : 'id';
                            $tmd_value = $value ? $value->getAttributeTranslated($column, $lang) : '';
                            //$value = Cache::get($request->domain.'-prepare_list_data-relation-'.$sl.$lang)!=null?Cache::get($request->domain.'-prepare_list_data-relation-'.$sl.$lang):Cache::put($request->domain.'-prepare_list_data-relation-'.$sl.$lang,$v->getAttributeTranslated($column, $lang),time()+30*24*3600);
                            //$tmd_value = $value ? $value : '';
                            $value = $tmd_value ? $tmd_value . '$$' . $value->id . '$$' . $value->slug : '';
                            break;
                    }

                    $domain = '';
                    $domainArray = Domain::where('site_id', $v['site_id'])->first();
                    if($domainArray!=""){
                        $domain = $domainArray->fqdn;
                    }

                    $cell = [
                        'value' => $value,
                        'slug' => $v['slug'],
                        'domain' => $domain,
                        'config' => $conf
                    ];
                    array_push($row, $cell);
                }

                $index = $this->translateDigit($sl, $lang);

                array_push($list_items, ['sl' => $index, 'row' => $row, 'record' => $v]);
            }
           // dd($list_items);

            $data['list'] = [
                'columns' => $columns,
                'items' => $list_items,
                'pagination' => $items->withPath('')->links(),
                'title' => isset($config['title']) ? $config['title'] : $content_types->lang($lang)->name,
                'is_archive' => false, //$archivingConfig == false ? false : $is_archive,
                'toolbar' => [
                    'search' => $searchConfig,
                    'filters' => $filters
                ]
            ];
            if ($is_aggregated and $layers) {
                $data['list']['toolbar']['layers']  = $layers;
            }
        }
        // die();
        //trace_log($data['list']['toolbar']);
        return $data;
    }


    private function prepare_details_data($request, $content_types, $config, $slug)
    {
        $lang = $request->lang;
        $detailsConfig = $config['columns'];
        // $item = call_user_func_array(array($content_types->table_name, 'where'), ['slug', $slug]);
        // $item = call_user_func_array(array($item, 'where'), ['publish', 1]);
        // $item = call_user_func(array($item, 'first'));

        $item = $content_types->table_name::where('slug', $slug)->where('publish', 1)->first();

        $details_items = [];
        if (isset($detailsConfig)) {
            // foreach ($columns as $k1 => $v1) {
            //     $type = $v1['type'];
            //     $conf = $v1;
            //     $field_name = $v1['name'];
            //     $value = $item->{$field_name};

            //     if ($lang != 'bn' && in_array($field_name, $item->translatable)) {
            //         $value = $item->lang($lang)->{$field_name};
            //     }


            //     switch ($type) {
            //         case 'text':
            //             $value = stripslashes($value);
            //             break;
            //         case 'taxonomy':
            //             $fieldName = $field_name . "_taxonomy";
            //             $taxonomy = $item->{$fieldName};
            //             $value = $taxonomy ? $taxonomy->name : '';
            //             break;
            //         case 'date':
            //             $value = date('d-m-Y', strtotime($value));
            //             $value = $lang == 'bn' ? $this->bn_digit($value) : $value;
            //             break;
            //         case 'datetime':
            //             $value = date('d-m-Y H:i:s', strtotime($value));
            //             $value = $lang == 'bn' ? $this->bn_digit($value) : $value;
            //             break;
            //     }

            //     $cell = [
            //         'value' => $value,
            //         'config' => $conf
            //     ];
            //     array_push($details_items, $cell);
            // }

            $data['item'] = [
                'columns' => $detailsConfig,
                'data' => $item->attachment
            ];
        }

        $data['view_code'] = 'details-common-default';

        trace_log($data);
        return $data;
    }

    private function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    private function bn_digit($str)
    {
        $search = array("0", "1", "2", "3", "4", "5", '6', "7", "8", "9");
        $replace = array("০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯");
        return str_replace($search, $replace, $str);
    }

    public function translateDigit($str, $lang = 'bn')
    {
        switch ($lang) {
            case 'bn':
                $str = $this->bn_digit($str);
                break;
        }

        return $str;
    }
    private function merge_input($data, $input)
    {
        foreach ($input as $key => $value) {
            $data[$key] = $value;
            $data['dd'] = 'dd';
        }
    }

    private function get_url($request)
    {
        return $request->protocol . '://' . $request->domain . '/' . $request->lang;
    }

    
    public function create_content(Request $request, $end_point)
    {
        $API_KEY_FOR_NOTHI = 'aPmnN3n8Qb';
        if (empty($request["api_key"]) || $request["api_key"] != $API_KEY_FOR_NOTHI) {
            return $this->sendResponse($request['api_key'] . ' api_key not match', $request['api_key']);
        }else{
            $file = new File();
    
            switch($end_point) {
                case "Notices":
                    $notice = Notices::create($this->common_attribute($request));
                    // $image = $file->nothiFile($request['image']);
                    // trace_log($file);
                    // $translate = Attribute::create($this->translate_data($request, $notice['id']));
                    
                    $create_data = json_encode($notice);
                    break;
                    
                case "News":
                    $news = News::create($this->common_attribute($request));
                    $create_data = json_encode($news);
                    break;
            }
            
            // trace_log($create_data);
            return $this->sendResponse($create_data, 'successful');
        }
        
    }

    public function common_attribute($commonData)
    {
        $data = [
            'title'         => $commonData->title_en,
            'body'          => $commonData->body_en,
            'publish_date'  => $commonData->publish_date,
            'archive_date'  => $commonData->archive_date,
            'site_id'       => $commonData->site['id'],

        ];
        return $data;
    }

    public function file_save($fileData)
    {
        $fileData->image = Input::file('file_data');
    }

    public function translate_data($data_en, $model_type, $id)
    {
        DB::connection('tenant')->table('rainlab_translate_attributes')->insert(
            [
                'model_id'      =>  $id,
                'locale'        => 'en',
                'model_type'    => $model_type,
                'attribute_data'=> json_encode($data_en),
            ]
        );
        return $this;
    }

    public function generateDiskName($data)
    {
        $ext = strtolower($data['extension']);
        $name = str_replace('.', '', uniqid(null, true));
        $diskName = !empty($ext) ? $name . '.' . $ext : $name;

        return $diskName;
    }



    public function generateStoragePath($data)
    {
        $getPathFromDiskName = str_split($this->generateDiskName($data), 3);
        $generatedPath = $data['uuid'].'/'.$getPathFromDiskName[0].'/'.$getPathFromDiskName[1].'/'.$getPathFromDiskName[2].'/';

        return $generatedPath;
    }

    public function makeCustomValidation($data)
    {
        if ($data['title_bn'] == '') {
            $message = 'The নাম bn field is required!';
        }
        if ($data['title_en'] == '') {
            $message = 'The নাম en field is required!';
        }
        if ($data['archive_date'] == '') {
            $message = 'The Archive Date field is required!';
        }

        if ($data['publish_date'] == '') {
            $publish_date = Carbon::today();
        }else {
            $publish_date = $data['publish_date'];
        }
        if ($publish_date > $data['archive_date']) {
            $message = 'The Archive Date must be equal or greater than publish date!';
        }

        if (isset($message)) {
            return ['status' => 'error', 'message' => $message];
        } else {
            return 'success';
        }

    }

    public function nothiNotice(Request $request){
        // return response()->json(['status'=>'Test','message' => 'Test']);die();
        $API_KEY_FOR_NOTHI = 'aPmnN3n8Qb';
        if (empty($request->api_key) || $request->api_key != $API_KEY_FOR_NOTHI) {
            return response()->json(['status'=>'error','message' => 'API key does not match!']);
        }

        // ------Notice data validation start----------

        $validation = $this->makeCustomValidation($request->all());


        if ($validation != 'success') {
            return response()->json($validation);
        }

        // ------Notice attachments validation start----------

        for ($i=1; $i < 5; $i++) { 
            $atc = 'attachment_'.$i;
            if (isset($request->$atc) && $request->$atc != '') {

                    try {
                        file_get_contents($request->$atc);
                    } catch (\Exception $e) {
                        return response()->json(['status'=>'error', 'field'=>$atc, 'message' => 'Invalid file link!']);
                    }

                    $path_info = pathinfo($request->$atc);
                    if ($path_info['extension'] != 'pdf' && $path_info['extension'] != 'PDF') {
                        return response()->json(['status'=>'error', 'field'=>$atc, 'message' => 'File format not supported! Use only pdf!']);
                    }
    
                    if (strlen(file_get_contents($request->$atc)) > 10000000) {
                        return response()->json(['status'=>'error', 'field'=>$atc, 'message' => 'File size can not be grater then 10 MB!']);
                    }
            }
        }

        // ------Notice attachments validation end----------


        // ------Notice image validation start----------

        if (isset($request->image) && $request->image != '') {

            try {
                file_get_contents($request->image);
            } catch (\Exception $e) {
                return response()->json(['status'=>'error', 'message' => 'Invalid image link!']);
            }

            $path_info = pathinfo($request->image);
            if ($path_info['extension'] != 'jpg' && $path_info['extension'] != 'png' && $path_info['extension'] != 'jpeg') {
                return response()->json(['status'=>'error','message' => 'Image format not supported! Use only jpg, jpeg or png!']);
            }

            if (strlen(file_get_contents($request->image)) > 4000000) {
                return response()->json(['status'=>'error','message' => 'Image size can not be grater then 4 MB!']);
            }

        }

        // ------Notice image validation end----------

            if ($request->publish_date == '') {
                $publish_date = Carbon::today();
            }else {
                $publish_date = $request->publish_date;
            }
            
            $api_data['title']        = $request->title_bn;
            $api_data['body']         = $request->body_bn;
            $api_data['publish_date']  = $publish_date;
            $api_data['archive_date']  = $request->archive_date;
            $api_data['site_id']       = $request->site['id'];
            $api_data['publish']       = $request->publish;

            $data_en = [
                'title'         => $request->title_en,
                'body'          => $request->body_en,    
            ];

            $notice = Notices::create($api_data);
            $link = $notice['slug'];
            $model_type = 'Np\Contents\Models\Notices';

            $this->translate_data($data_en,$model_type, $notice['id']);

        
        for ($i=1; $i < 5; $i++) {
            $atc = 'attachment_'.$i;
            if (isset($request->$atc) && $request->$atc != '') {

                $path_info = pathinfo($request->$atc);
                $file_info = [
                    'url' => $request->$atc,
                    'uuid' => $request->site['uuid'],
                    'extension' => $path_info['extension'],
                ];

                $file = new File();
                $file->fromUrl($file_info['url']);
                $file->is_public = true;
                $file->save();

                $updata['attachment_id'] = $notice['id'];
                $updata['attachment_type'] = 'Np\Contents\Models\Notices';
                $updata['field'] = 'attachments';
                $updata['is_public'] = $request->publish;
                File::where('id',$file->id)->update($updata);

            }
        }

        if (isset($request->image) && $request->image != '') {
            $path_info = pathinfo($request->image);
            $file_info = [
                'url' => $request->image,
                'uuid' => $request->site['uuid'],
                'extension' => $path_info['extension'],
            ];


            $file = new File();
            $file->fromUrl($file_info['url']);
            $file->is_public = true;
            $file->save();

            $updata['attachment_id'] = $notice['id'];
            $updata['attachment_type'] = 'Np\Contents\Models\Notices';
            $updata['field'] = 'image';
            $updata['is_public'] = $request->publish;
            File::where('id',$file->id)->update($updata);
        }


        return response()->json(
            [
            'status'=>'success',
            'view_link' => $request->protocol.'://'.$request->domain.'/'.$request->lang.'/site/notices/'.$link,
            'message' => 'Notice created successfully!'
            ]
        );

    }

    public function nothiNews(Request $request){

        $API_KEY_FOR_NOTHI = 'aPmnN3n8Qb';
        if (empty($request->api_key) || $request->api_key != $API_KEY_FOR_NOTHI) {
            return response()->json(['status'=>'error','message' => 'API key does not match!']);
        }

        // ------News data validation start----------

        $validation = $this->makeCustomValidation($request->all());

        if ($validation != 'success') {
            return response()->json($validation);
        }

        // ------News data validation end----------

        
        // ------News attachments validation start----------

        for ($i=1; $i < 5; $i++) { 
            $atc = 'attachment_'.$i;
            if (isset($request->$atc) && $request->$atc != '') {

                    try {
                        file_get_contents($request->$atc);
                    } catch (\Exception $e) {
                        return response()->json(['status'=>'error', 'field'=>$atc, 'message' => 'Invalid file link!']);
                    }

                    $path_info = pathinfo($request->$atc);
                    if ($path_info['extension'] != 'pdf' && $path_info['extension'] != 'PDF') {
                        return response()->json(['status'=>'error', 'field'=>$atc, 'message' => 'File format not supported! Use only pdf!']);
                    }
    
                    if (strlen(file_get_contents($request->$atc)) > 10000000) {
                        return response()->json(['status'=>'error', 'field'=>$atc, 'message' => 'File size can not be grater then 10 MB!']);
                    }
            }
        }

        // ------News attachments validation end----------


        // ------News images validation start----------

        for ($i=1; $i < 5; $i++) { 
            $img = 'image_'.$i;
            if (isset($request->$img) && $request->$img != '') {

                try {
                    file_get_contents($request->$img);
                } catch (\Exception $e) {
                    return response()->json(['status'=>'error','field'=>$img, 'message' => 'Invalid image link!']);
                }
    
                $path_info = pathinfo($request->$img);
                if ($path_info['extension'] != 'jpg' && $path_info['extension'] != 'png' && $path_info['extension'] != 'jpeg') {
                    return response()->json(['status'=>'error', 'field'=>$img, 'message' => 'Image format not supported! Use only jpg, jpeg or png!']);
                }
    
                if (strlen(file_get_contents($request->$img)) > 4000000) {
                    return response()->json(['status'=>'error', 'field'=>$img, 'message' => 'Image size can not be grater then 4 MB!']);
                }
            }
        }

        // ------News images validation end----------


            if ($request->publish_date == '') {
                $publish_date = Carbon::today();
            }else {
                $publish_date = $request->publish_date;
            }
            
            $api_data['title']        = $request->title_bn;
            $api_data['body']         = $request->body_bn;
            $api_data['publish_date']  = $publish_date;
            $api_data['archive_date']  = $request->archive_date;
            $api_data['site_id']       = $request->site['id'];
            $api_data['publish']       = $request->publish;

            $data_en = [
                'title'         => $request->title_en,
                'body'          => $request->body_en,    
            ];

            $news = News::create($api_data);
            $link = $news['slug'];
            $model_type = 'Np\Contents\Models\News';

            $this->translate_data($data_en,$model_type, $news['id']);
        
        for ($i=1; $i < 5; $i++) {
            $atc = 'attachment_'.$i;
            if (isset($request->$atc) && $request->$atc != '') {

                $path_info = pathinfo($request->$atc);
                $file_info = [
                    'url' => $request->$atc,
                    'uuid' => $request->site['uuid'],
                    'extension' => $path_info['extension'],
                ];


                $file = new File();
                $file->fromUrl($file_info['url']);
                $file->is_public = true;
                $file->save();

                $updata['attachment_id'] = $news['id'];
                $updata['attachment_type'] = 'Np\Contents\Models\News';
                $updata['field'] = 'attachments';
                $updata['is_public'] = $request->publish;
                File::where('id',$file->id)->update($updata);

            }
        }

        for ($i=1; $i < 5; $i++) { 
            $img = 'image_'.$i;
            if (isset($request->$img) && $request->$img != '') {
                $path_info = pathinfo($request->$img);
                $file_info = [
                    'url' => $request->$img,
                    'uuid' => $request->site['uuid'],
                    'extension' => $path_info['extension'],
                ];

                $file = new File();
                $file->fromUrl($file_info['url']);
                $file->is_public = true;
                $file->save();

                $updata['attachment_id'] = $news['id'];
                $updata['attachment_type'] = 'Np\Contents\Models\News';
                $updata['field'] = 'images';
                $updata['is_public'] = $request->publish;
                File::where('id',$file->id)->update($updata);
                
            }
        }

        return response()->json(
            [
            'status'=>'success',
            'view_link' => $request->protocol.'://'.$request->domain.'/'.$request->lang.'/site/news/'.$link,
            'message' => 'News created successfully!'
            ]
        );

    }
}
