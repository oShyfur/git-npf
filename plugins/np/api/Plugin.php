<?php

namespace Np\Api;

use Event;
use System\Classes\PluginBase;
use Illuminate\Routing\Router;
use Np\Structure\Middleware\InitializedTenantFileSystem;
use Np\Structure\Middleware\InitializedTenantDB;
use Np\Structure\Classes\BackendUserExtension;
use Np\Structure\Classes\EventsHandler;
use Np\Structure\Classes\BackendUserRoleExtension;
use Np\Structure\Classes\NP;
use RainLab\Translate\Models\Message;
use Np\Structure\Models\Domain;
use Np\Structure\Models\Site;
use Np\Contents\Models\Taxonomy;
use Np\Contents\Models\Files;
use Np\Contents\Models\OfficerList;
use Np\Contents\Models\Teachers;
use Np\Contents\Models\DcSectionWiseForm;
use Np\Contents\Models\LawPolicy;
use Np\Contents\Models\Meeting;
use Np\Contents\Models\Protibedon;
use Np\Contents\Models\StaffList;
use Np\Contents\Models\DcofficeSection;
use Np\Contents\Models\DivcomSection;
use Np\Contents\Models\InfoOfficer;
use Np\Contents\Models\Onik;
use Np\Structure\Models\Layer;
use Illuminate\Support\Facades\DB;

class Plugin extends PluginBase
{

    public $elevated = true;


    public function boot()
    {
    }

    // twig custom function for site partials
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                '_t'  => [$this, 'translateString'],
                '_d'  => [$this, 'translateDigit'],
                //'__' => [$this, 'translatePlural'],
                //'localeUrl' => [$this, 'localeUrl'],
            ],
            'functions' => [
                'generate_link' => function ($link, $lang = 'bn') {

                    $isExternal = false;
                    if (strpos($link, 'www') === 0) {
                        $link = 'http://' . $link;
                        $isExternal = true;
                    }

                    if (strpos($link, 'http') === 0) {
                        $isExternal = true;
                    }


                    return  $isExternal ? $link : '/' . $lang . '/' . ltrim($link, '/');
                },
                'relation_link' => function ($label, $model, $relationName, $slug, $lang) {
                    $fullModel = "Np\Contents\Models\\" . $model;
                    $relatedModel = call_user_func([$fullModel, $relationName]);
                    $relatedModel = class_basename(call_user_func([$relatedModel, 'getRelated']));
                    $viewCode = NP::CamelCaseToSnakeCase($relatedModel);

                    return '<a href="' . NP::detailsLink($lang, $viewCode, $slug) . '">' . $label . '</a>';
                },
                'view_link' => function ($code, $lang) {

                    return NP::viewLink($code, $lang);
                },

                'details_link' => function ($code, $lang, $slug) {
                    $code = NP::CamelCaseToSnakeCase($code);
                    return NP::detailsLink($lang, $code, $slug);
                },

                'domain_url' => function ($site_id) {
                    $domain = Domain::with('site')->where('site_id', $site_id)->first()->fqdn;
                    return $domain;
                },

                'site_uuid' => function ($site_id) {
                    $uuid = Site::where('id', $site_id)->first()->uuid;
                    return $uuid;
                },

                'sub_dir_path' => function ($disk_name) {
                    $sub_path = substr($disk_name, 0, 3).'/'.substr($disk_name, 3, 3).'/'.substr($disk_name, 6, 3);
                    return $sub_path;
                },

                'taxonomy_name' => function ($taxonomy,$lang) {
                    $texonomyArray = Taxonomy::where('id', $taxonomy)->first();
                    $texonomyValue = $texonomyArray->getAttributeTranslated('name', $lang);
                    return $texonomyValue;
                },

                'content_files_data' => function ($content_id,$lang) {
                    $linkFileArray = Files::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $linkFileValue = $linkFileArray->getAttributeTranslated('title', $lang);
                    return $linkFileArray;
                },

                'content_officersList_data' => function ($content_id,$lang) {
                    $contentArray = OfficerList::where('id', $content_id)->whereNull('deleted_at')->withoutGlobalScopes()->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_teachers_data' => function ($content_id,$lang) {
                    $contentArray = Teachers::where('id', $content_id)->whereNull('deleted_at')->withoutGlobalScopes()->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_DcSectionWiseForm_data' => function ($content_id,$lang) {
                    $contentArray = DcSectionWiseForm::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_LawPolicy_data' => function ($content_id,$lang) {
                    $contentArray = LawPolicy::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_Meeting_data' => function ($content_id,$lang) {
                    $contentArray = Meeting::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_Protibedon_data' => function ($content_id,$lang) {
                    $contentArray = Protibedon::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_StaffList_data' => function ($content_id,$lang) {
                    $contentArray = StaffList::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_DcofficeSection_data' => function ($content_id,$lang) {
                    $contentArray = DcofficeSection::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_DivcomSection_data' => function ($content_id,$lang) {
                    $contentArray = DivcomSection::where('id', $content_id)->whereNull('deleted_at')->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'layer_name' => function ($layer,$lang) {
                    $layerValue = '';
                    if ($lang != 'en') {
                        $attrData = DB::table('rainlab_translate_attributes')->select('attribute_data')->where('model_id', $layer)->Where('model_type', 'like', '%Layer%')->where('locale',$lang )->first();
                        if(!empty($attrData) && $attrData!=""){
                            $attrDataObj = json_decode($attrData->attribute_data);
                            if(property_exists($attrDataObj,'name' )){
                                $layerValue = $attrDataObj->name;
                            }
                        }
                    } else {
                        $layerArray = Layer::where('id', $layer)->first();
                        $layerValue = $layerArray->getAttributeTranslated('name', $lang);
                    }
                    return $layerValue;
                },

                'site_name' => function ($site,$lang) {
                    $siteValue = '';
                    if ($lang != 'en') {
                        $attrData = DB::table('rainlab_translate_attributes')->select('attribute_data')->where('model_id', $site)->Where('model_type', 'like', '%Site%')->where('locale',$lang )->first();
                        if(!empty($attrData) && $attrData!=""){
                            $attrDataObj = json_decode($attrData->attribute_data);
                            if(property_exists($attrDataObj,'name' )){
                                $siteValue = $attrDataObj->name;
                            }
                        }
                    } else {
                        $siteArray = Site::where('id', $site)->first();
                        $siteValue = $siteArray->getAttributeTranslated('name', $lang);
                    }
                    return $siteValue;
                },

                'site_name_list' => function ($siteIds,$lang) {
                    $siteData = [];
                    if ($lang != 'en') {
                        $attrData = DB::table('rainlab_translate_attributes')->select('attribute_data','model_id')->whereIn('model_id', $siteIds)->Where('model_type', 'like', '%Site%')->where('locale',$lang )->get();
                        if(!empty($attrData)){
                            foreach($attrData as $key => $val){
                                if(!empty($val) && $val!=""){
                                    $attrDataObj = json_decode($val->attribute_data);
                                    if(property_exists($attrDataObj,'name' )){
                                        $siteData[$val->model_id] = $attrDataObj->name;
                                    }
                                }
                            }
                        }                        
                    } else {
                        $siteArray = Site::select('id','name')->whereIn('id', $siteIds)->get();
                        if(!empty($siteArray)){
                            foreach($siteArray as $key => $val){
                                $siteData[$val->id] = $val->getAttributeTranslated('name', $lang);
                            }
                        } 
                    }
                    return $siteData;
                },

                'eDirectory_OfficerList_idArr' => function ($siteIds,$limit) {
                    $contentArray = OfficerList::whereIn('site_id', $siteIds)->where('publish', 1)->whereNull('deleted_at')->orderBy('site_id')->withoutGlobalScopes()->select('id')->paginate($limit);
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'eDirectory_officersList_data' => function ($content_ids,$lang) {
                    $contentArray = OfficerList::whereIn('id', $content_ids)->whereNull('deleted_at')->orderBy('site_id')->withoutGlobalScopes()->get();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'eDirectory_OfficerList_ids' => function ($site) {
                    $contentArray = OfficerList::where('site_id', $site)->where('publish', 1)->whereNull('deleted_at')->orderBy('sort_order')->orderBy('field_batch')->orderBy('id_number')->withoutGlobalScopes()->lists('id');
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'translate_digit' => function ($str,$lang) {
                    if ($lang == 'bn') {
                        $search = array("0", "1", "2", "3", "4", "5", '6', "7", "8", "9");
                        $replace = array("০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯");
                        return str_replace($search, $replace, $str);
                    } elseif ($lang == 'en') {
                        $search = array("০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯");
                        $replace = array("0", "1", "2", "3", "4", "5", '6', "7", "8", "9");
                        return str_replace($search, $replace, $str);
                    } else {
                        return $str;
                    }
                },

                'translate_month' => function ($str,$lang) {
                    if ($lang == 'bn') {
                        $search = array("January","February","March","April","May","June","July","August","September","October","November","December");
                        $replace = array("জানুয়ারি","ফেব্রুয়ারী","মার্চ","এপ্রিল","মে","জুন","জুলাই","আগষ্ট","সেপ্টেম্বর","অক্টোবর","নভেম্বর","ডিসেম্বর");
                        return str_replace($search, $replace, $str);
                    } elseif ($lang == 'en') {
                        $search = array("জানুয়ারি","ফেব্রুয়ারী","মার্চ","এপ্রিল","মে","জুন","জুলাই","আগষ্ট","সেপ্টেম্বর","অক্টোবর","নভেম্বর","ডিসেম্বর");
                        $replace = array("January","February","March","April","May","June","July","August","September","October","November","December");
                        return str_replace($search, $replace, $str);
                    } else {
                        return $str;
                    }
                },

                'child_sites_employeeList' => function ($site,$site_layer_id,$site_ministry_id,$ministry_id,$layer_id,$searchStringCode) {
                    // return ['site_id' => $site, 'site_layer_id' => $site_layer_id, 'layer_id'=>$layer_id, 'ministry_id' => $ministry_id, 'searchStringCode' => $searchStringCode];
                    if ($site_layer_id == 4) {
                        // this if works for Division Layer ID 4
                        $dataArray = Site::where('layer_id', $layer_id)->where('ministry_id', $ministry_id)->lists('id');
                        return $dataArray;
                    } elseif ($site_layer_id == 5 && ($searchStringCode == 'DC' || $searchStringCode == 'ADC-Overall' || $searchStringCode == 'DDLG' || $searchStringCode == 'ADC-Revenue' || $searchStringCode == 'ADC-LA' || $searchStringCode == 'SAC-Nezarot' || $searchStringCode == 'DFO')){
                        // this if works for District Layer ID 5 and district level designation
                        $dataArray = Site::where('id', $site)->where('layer_id', $layer_id)->where('ministry_id', $ministry_id)->lists('id');
                        return $dataArray;
                    } elseif ($site_layer_id == 5 && ($searchStringCode == 'UNO' || $searchStringCode == 'CJM' || $searchStringCode == 'SP')){
                        // this if works for District Layer ID 5 and offices under district
                        $dataArray = Site::where('parent_id', $site)->where('layer_id', $layer_id)->where('ministry_id', $ministry_id)->lists('id');
                        return $dataArray;
                    } elseif ($site_layer_id == 5 && ($searchStringCode == 'AC-Land' || $searchStringCode == 'ULO')){
                       // this if works for District Layer ID 5 and offices under upazila
                        $dataArray = DB::table('np_structure_sites as nss1')
                                    ->select('nss1.id')
                                    ->join('np_structure_sites as nss2', 'nss1.parent_id', '=', 'nss2.id')
                                    ->where('nss1.layer_id', $layer_id)
                                    ->where('nss1.ministry_id', $ministry_id)
                                    ->where('nss2.parent_id', $site)
                                    ->lists('nss1.id');
                        return $dataArray;
                    }  elseif ($site_layer_id == 6 && ($searchStringCode == 'UNO')){
                        // this if works for Upazilla Layer ID 5 and Upazilla level designation
                        $dataArray = Site::where('id', $site)->where('layer_id', $layer_id)->where('ministry_id', $ministry_id)->lists('id');
                        return $dataArray;
                    } elseif ($site_layer_id == 6 && ($searchStringCode == 'AC-Land' || $searchStringCode == 'ULO')){
                        // this if works for Upazilla Layer ID 5 and offices under Upazilla
                        $dataArray = Site::where('parent_id', $site)->where('layer_id', $layer_id)->where('ministry_id', $ministry_id)->lists('id');
                        return $dataArray;
                    } elseif ($site_layer_id == 7 && ($searchStringCode == 'ULO')){
                        // this if works for Upazilla Layer ID 5 and offices under Upazilla
                        $dataArray = Site::where('id', $site)->where('layer_id', $layer_id)->where('ministry_id', $ministry_id)->lists('id');
                        return $dataArray;
                    }
                },
 
                'employee_list_data' => function ($searchString,$childSites) {
                    $dataArray = OfficerList::whereIn('site_id', $childSites)->Where('publish', 1)->whereNull('deleted_at')->Where('designation', 'like', $searchString.'%')->withoutGlobalScopes()->get();
                    return $dataArray;
                },

                'child_sites_infoOfficer' => function ($selectedLayerId) {
                    // return ['layer_id'=>$selectedLayerId];
                    $dataArray = Site::where('layer_id', $selectedLayerId)->lists('id');
                    return $dataArray;
                },

                'child_sites_onik' => function ($selectedLayerId) {
                    // return ['layer_id'=>$selectedLayerId];
                    $dataArray = Site::where('layer_id', $selectedLayerId)->lists('id');
                    return $dataArray;
                },

                'infoOfficer_list_data' => function ($childSites,$limit) {
                    $dataArray = InfoOfficer::whereIn('site_id', $childSites)->whereNotNull('do')->Where('publish', 1)->whereNull('deleted_at')->orderBy('site_id')->withoutGlobalScopes()->select('do','slug')->simplePaginate($limit);
                    return $dataArray;
                },

                'onik_list_data' => function ($childSites,$limit) {
                    $dataArray = Onik::whereIn('site_id', $childSites)->whereNotNull('do')->Where('publish', 1)->whereNull('deleted_at')->orderBy('site_id')->withoutGlobalScopes()->select('do','slug')->simplePaginate($limit);
                    return $dataArray;
                },

                'content_infoOfficer_officer_data' => function ($content_id,$lang) {
                    $contentArray = OfficerList::where('id', $content_id)->orWhere('slug', $content_id)->whereNull('deleted_at')->withoutGlobalScopes()->first();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'content_onik_officer_data' => function ($content_id,$lang) {
                    $contentArray = OfficerList::where('id', $content_id)->orWhere('slug', $content_id)->whereNull('deleted_at')->withoutGlobalScopes()->get();
                    // $contentValue = $contentArray->getAttributeTranslated('title', $lang);
                    return $contentArray;
                },

                'urldecode' => function ($string) {
                    $contentArray = urldecode($string);
                    return $contentArray;
                },

                'customPaginationPrevious' => function ($viewCode,$filterCode,$filterValue,$currentPage) {
                    $previousPage = $currentPage - 1;
                    $contentArray = $viewCode."?".$filterCode."=".$filterValue."&page=".$previousPage;
                    return $contentArray;
                },

                'customPaginationNext' => function ($viewCode,$filterCode,$filterValue,$currentPage) {
                    $nextPage = $currentPage + 1;
                    $contentArray = $viewCode."?".$filterCode."=".$filterValue."&page=".$nextPage;
                    return $contentArray;
                },

                'domain_site_id' => function ($fqdn) {
                    $site_id = Domain::where('fqdn', $fqdn)->whereNull('deleted_at')->where('status',1)->first()->site_id;
                    return $site_id;
                },
            ],
        ];
    }

    public function translateString($string,$locale, $params = [] )
    {
        return Message::trans($string, $params, $locale);
    }

    public function translateDigit($string,$locale)
    {
        switch($locale)
        {
            case 'bn':
                $string = $this->bn_digit($string);
                break;

        }

        return $string;

    }

    private function bn_digit($str)
    {
        $search = array("0", "1", "2", "3", "4", "5", '6', "7", "8", "9");
        $replace = array("০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯");
        return str_replace($search, $replace, $str);
    }


}
