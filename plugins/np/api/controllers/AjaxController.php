<?php

namespace Np\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Np\Contents\Models\SiteFeedback;
use Np\Structure\Models\Domain;
use Np\Structure\Models\Site;
use Response;
use Validator;

/**
 * Sample Resourceful Test Controller
 */
class AjaxController extends APIBaseController
{

    public $defaultTheme = 'default';
    public $domain = null;
    public $lang = 'bn';

    public function __construct(Request $request)
    {
    }

    public function ajax()
    {
        $request = request();

        $data = [];

        if (
            method_exists($this, $request->action)
            && is_callable(array($this, $request->action))
        ) {
            $data = call_user_func_array(
                array($this, $request->action),
                [$request]
            );
        }

        return $data;
    }

    public function onSubmitForm($request)
    {
        $data = [];
        parse_str(input('formData'), $data);

        $data['form_code'] = input('form_code');
        $data['domain'] = input('domain');

        $response = [
            'success' => false,
            'data' => ''
        ];

        $rules = [
            'form_code' => 'required',
            'domain' => 'required'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $response['errors'] = $validator->messages();
        } else {
            //save data to db

            $feedback = new SiteFeedback;
            $feedback->form_id = request('form_code');
            $feedback->site_id = input('site.id');
            $feedback->data = $data;
            $feedback->save();

            $response['data'] = $feedback->id;
            $response['success'] = true;
            $response['message'] = 'Thanks for your submittion';
        }

        return Response::json($response);
    }

    public function getRedirectDomain($request)
    {
        $domain = '';
        if($request->input('alldiv')){
            $option_level = $request->input('option_level');
            //return $request->input('option_level');

            $field = '';
            //if($option_level == 'first_level'){
            $val =  $request->input('id');
            //return $val;
            // $domain = DB::table('npf_domains')->select('subdomain as fqdn')->where('id',$val)->get()->toArray();
            $domain = DB::table('np_structure_domains')->select('fqdn as fqdn')->where('site_id',$val)->groupBy('site_id')->get()->toArray();
            //return $domain;
            // }


        }else {
            if ($siteId = $request->input('id'))
                $domain = Domain::where('site_id', $siteId)->first();
        }

        return $domain;
    }


    public function getDoptors($request)
    {
        $doptors = [];
        $layer = $request->input('layer');
        $elValue = $request->input('elValue');
        $lang = $request->input('lang');
        $currentHostId  = $request->input('currentHostId');
        
        switch ($request->input('el')) {
            case 'doptor':
                if($elValue=='alldiv'){
                    // $getLayerData = DB::table('npf_domains')->select('subdomain as fqdn')->where('id',$currentHostId)->where('domain_type_id',14)->get()->toArray();
                    $getLayerData = DB::table('np_structure_sites as nss')
                                    ->select('nsd.fqdn as fqdn')
                                    ->join('np_structure_domains as nsd', 'nsd.site_id', '=', 'nss.id')
                                    ->where('nss.id',$currentHostId)
                                    ->where('nss.site_type_id',14)
                                    ->get()
                                    ->toArray();
                    //return $getLayerData;
                    if(!empty($getLayerData)){
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('nsd.fqdn', $getLayerData[0]->fqdn)->where('np_structure_sites.active', 1)->groupBy('nsd.site_id')->get()->toArray();
                   // return $doptors;
                    } else {
                        //$doptors = DB::table('np_structure_geo_divisions')->select('*')->get()->toArray();
                        // $doptors = DB::table('npf_domains')->select('id', 'sitename_bn as name_bng', 'domain_type_id', 'sitename_en as name_eng', 'subdomain as domain')->where('domain_type_id', 4)->where('parent_id', 1)->get()->toArray();
                        $doptors = DB::table('np_structure_sites as nss')
                                    ->select('nss.id', 'nss.name as name', 'nss.site_type_id', 'nsd.site_id as domain')
                                    ->join('np_structure_domains as nsd', 'nsd.site_id', '=', 'nss.id')
                                    ->where('nss.site_type_id', 4)
                                    ->where('nss.parent_id', 1)
                                    ->groupBy('nsd.site_id')
                                    ->get()
                                    ->toArray();
                        //return $doptors;
                        // Traslate Sitename
                        if ($lang != 'en') {
                            if (!empty($doptors)) {
                                $i=0;
                                foreach ($doptors as $value){
                                    // echo $doptors[$i]->name."<br><br>";
                                    // echo "<pre>";
                                    // print_r($doptors);
                                    if(isset($doptors[$i]->name)) {
                                        $doptors[$i]->name = $this->getRainlabAttribute($doptors[$i]->id, $lang);
                                    }
                                    $i++;
                                }
                            }
                        }
                        // Find Domain
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                if(isset($doptors[$i]->id)){
                                    $id = $doptors[$i]->id;
                                    $domainName = DB::table('np_structure_domains')->where('site_id',$id)->first();
                                    $doptors[$i]->domain = $domainName->fqdn;
                                }
                                $i++;
                            }
                        }
                    }
                } else {
                    if (in_array($elValue, [4, 5, 6, 7])) {
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('layer_id', 4)->where('site_type_id', 4)->where('np_structure_sites.active', 1)->groupBy('nsd.site_id')->get()->toArray();
                    }

                    if ($lang != 'en') {
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                if(isset($doptors[$i]['name']))
                                    $doptors[$i]['name'] = $this->getRainlabAttribute($doptors[$i]['id'], $lang);
                                $i++;
                            }
                        }
                    }

                    if (!empty($doptors)) {
                        $i=0;
                        foreach ($doptors as $value){
                            if(isset($doptors[$i]['id'])){
                                $id = $doptors[$i]['id'];
                                $domainName = DB::table('np_structure_domains')->where('site_id',$id)->first();
                                $doptors[$i]['domain'] = $domainName->fqdn;
                            }
                            $i++;
                        }
                    }
                }
                break;

            case 'first_level':
                if($layer=='alldiv'){
                    //$doptors = DB::table('np_structure_geo_districts')->select('*')->where('geo_division_id', $elValue)->get()->toArray();
                    // $doptors = DB::table('npf_domains')->select('id','sitename_bn as name_bng', 'sitename_en as name_eng', 'subdomain as domain')->where('domain_type_id',5)->where('parent_id',$elValue)->get()->toArray();
                    $doptors = DB::table('np_structure_sites as nss')
                                ->select('nss.id', 'nss.name as name', 'nss.site_type_id', 'nsd.site_id as domain')
                                ->join('np_structure_domains as nsd', 'nsd.site_id', '=', 'nss.id')
                                ->where('nss.site_type_id', 5)
                                ->where('nss.parent_id', $elValue)
                                ->groupBy('nsd.site_id')
                                ->get()
                                ->toArray();
                    // Traslate Sitename
                    if ($lang != 'en') {
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                // echo $doptors[$i]->name."<br><br>";
                                // echo "<pre>";
                                // print_r($doptors);
                                if(isset($doptors[$i]->name)) {
                                    $doptors[$i]->name = $this->getRainlabAttribute($doptors[$i]->id, $lang);
                                }
                                $i++;
                            }
                        }
                    }
                    // Find Domain
                    if (!empty($doptors)) {
                        $i=0;
                        foreach ($doptors as $value){
                            if(isset($doptors[$i]->id)){
                                $id = $doptors[$i]->id;
                                $domainName = DB::table('np_structure_domains')->where('site_id',$id)->first();
                                $doptors[$i]->domain = $domainName->fqdn;
                            }
                            $i++;
                        }
                    }
                } else {
                    if ($layer == 4) {
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('layer_id', 4)->where('site_type_id', 14)->where('parent_id', $elValue)->orWhere('np_structure_sites.id', $elValue)->where('np_structure_sites.active', 1)->groupBy('nsd.site_id')->get()->toArray();
                    } elseif (in_array($layer, [5, 6, 7])) {
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('layer_id', 5)->where('parent_id', $elValue)->where('ministry_id', 5)->where('active', 1)->groupBy('nsd.site_id')->get()->toArray();
                    }

                    if ($lang != 'en') {
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                if(isset($doptors[$i]['name']))
                                    $doptors[$i]['name'] = $this->getRainlabAttribute($doptors[$i]['id'], $lang);
                                $i++;
                            }
                        }
                    }

                    if (!empty($doptors)) {
                        $i=0;
                        foreach ($doptors as $value){
                            if(isset($doptors[$i]['id'])){
                                $id = $doptors[$i]['id'];
                                $domainName = DB::table('np_structure_domains')->where('site_id',$id)->first();
                                $doptors[$i]['domain'] = $domainName->fqdn;
                            }
                            $i++;
                        }
                    }
                }
                break;

            case 'second_level':
                if($layer=='alldiv'){
                    //$doptors = DB::table('np_structure_geo_upazilas')->select('*')->where('geo_district_id', $elValue)->get()->toArray();
                    // $doptors = DB::table('npf_domains')->select('id','sitename_bn as name_bng', 'sitename_en as name_eng', 'subdomain as domain')->where('domain_type_id',6)->where('parent_id',$elValue)->get()->toArray();
                    $doptors = DB::table('np_structure_sites as nss')
                                ->select('nss.id', 'nss.name as name', 'nss.site_type_id', 'nsd.site_id as domain')
                                ->join('np_structure_domains as nsd', 'nsd.site_id', '=', 'nss.id')
                                ->where('nss.site_type_id', 6)
                                ->where('nss.parent_id', $elValue)
                                ->groupBy('nsd.site_id')
                                ->get()
                                ->toArray();
                    // Traslate Sitename
                    if ($lang != 'en') {
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                // echo $doptors[$i]->name."<br><br>";
                                // echo "<pre>";
                                // print_r($doptors);
                                if(isset($doptors[$i]->name)) {
                                    $doptors[$i]->name = $this->getRainlabAttribute($doptors[$i]->id, $lang);
                                }
                                $i++;
                            }
                        }
                    }
                    // Find Domain
                    if (!empty($doptors)) {
                        $i=0;
                        foreach ($doptors as $value){
                            if(isset($doptors[$i]->id)){
                                $id = $doptors[$i]->id;
                                $domainName = DB::table('np_structure_domains')->whereNull('deleted_at')->where('site_id',$id)->first();
                                $doptors[$i]->domain = $domainName->fqdn;
                            }
                            $i++;
                        }
                    }
                } else {

                    if ($layer == 5) {
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('layer_id', 5)->where('parent_id', $elValue)->orWhere('np_structure_sites.id', $elValue)->where('np_structure_sites.active', 1)->groupBy('nsd.site_id')->get()->toArray();
                    } elseif (in_array($layer, [6, 7])) {
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('layer_id', 6)->where('parent_id', $elValue)->where('ministry_id', 5)->where('np_structure_sites.active', 1)->groupBy('nsd.site_id')->get()->toArray();
                    }

                    if ($lang != 'en') {
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                if(isset($doptors[$i]['name']))
                                    $doptors[$i]['name'] = $this->getRainlabAttribute($doptors[$i]['id'], $lang);
                                $i++;
                            }
                        }
                    }

                    if (!empty($doptors)) {
                        $i=0;
                        foreach ($doptors as $value){
                            if(isset($doptors[$i]['id'])){
                                $id = $doptors[$i]['id'];
                                $domainName = DB::table('np_structure_domains')->whereNull('deleted_at')->where('site_id',$id)->first();
                                $doptors[$i]['domain'] = $domainName->fqdn;
                            }
                            $i++;
                        }
                    }
                }
                break;

            case 'third_level':
                if($layer=='alldiv'){
                    //$doptors = DB::table('np_structure_geo_unions')->select('*')->where('geo_upazila_id', $elValue)->get()->toArray();
                    // $doptors = DB::table('npf_domains')->select('id','sitename_bn as name_bng', 'sitename_en as name_eng', 'subdomain as domain')->where('domain_type_id',7)->where('parent_id',$elValue)->get()->toArray();
                    $doptors = DB::table('np_structure_sites as nss')
                                ->select('nss.id', 'nss.name as name', 'nss.site_type_id', 'nsd.site_id as domain')
                                ->join('np_structure_domains as nsd', 'nsd.site_id', '=', 'nss.id')
                                ->where('nss.site_type_id', 7)
                                ->where('nss.parent_id', $elValue)
                                ->groupBy('nsd.site_id')
                                ->get()
                                ->toArray();
                    // Traslate Sitename
                    if ($lang != 'en') {
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                // echo $doptors[$i]->name."<br><br>";
                                // echo "<pre>";
                                // print_r($doptors);
                                if(isset($doptors[$i]->name)) {
                                    $doptors[$i]->name = $this->getRainlabAttribute($doptors[$i]->id, $lang);
                                }
                                $i++;
                            }
                        }
                    }
                    // Find Domain
                    if (!empty($doptors)) {
                        $i=0;
                        foreach ($doptors as $value){
                            if(isset($doptors[$i]->id)){
                                $id = $doptors[$i]->id;
                                $domainName = DB::table('np_structure_domains')->where('site_id',$id)->first();
                                $doptors[$i]->domain = $domainName->fqdn;
                            }
                            $i++;
                        }
                    }
                } else {
                    if ($layer == 6) {
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('layer_id', 6)->where('parent_id', $elValue)->orWhere('np_structure_sites.id', $elValue)->where('np_structure_sites.active', 1)->groupBy('nsd.site_id')->get()->toArray();
                    } elseif (in_array($layer, [7])) {
                        $doptors = Site::select('np_structure_sites.*','nsd.site_id as domain')->join('np_structure_domains as nsd','nsd.site_id','=','np_structure_sites.id')->where('layer_id', 7)->where('parent_id', $elValue)->where('np_structure_sites.active', 1)->groupBy('nsd.site_id')->get()->toArray();
                    }

                    if ($lang != 'en') {
                        if (!empty($doptors)) {
                            $i=0;
                            foreach ($doptors as $value){
                                if(isset($doptors[$i]['name']))
                                    $doptors[$i]['name'] = $this->getRainlabAttribute($doptors[$i]['id'], $lang);
                                $i++;
                            }
                        }
                    }

                    if (!empty($doptors)) {
                        $i=0;
                        foreach ($doptors as $value){
                            if(isset($doptors[$i]['id'])){
                                $id = $doptors[$i]['id'];
                                $domainName = DB::table('np_structure_domains')->where('site_id',$id)->first();
                                $doptors[$i]['domain'] = $domainName->fqdn;
                            }
                            $i++;
                        }
                    }
                }
                break;
        }
        return $doptors;
    }

    public function getRainlabAttribute($siteId,$lang){
        $name='';
        if ($lang != 'en') {
            $attrData = DB::table('rainlab_translate_attributes')->select('attribute_data')->where('model_id', $siteId)->Where('model_type', 'like', '%Site%')->where('locale',$lang )->first();
            if(!empty($attrData) && $attrData!=""){
                $attrDataObj = json_decode($attrData->attribute_data);
                if(property_exists($attrDataObj,'name' )){
                    $name = $attrDataObj->name;
                }
            }
        }
        return $name;
    }

    public function getHostData($request){
        //$doptors = DB::table('npf_domains')->select('id','sitename_bn as name_bng', 'sitename_en as name_eng')->where('domain_type_id',6)->where('parent_id',$elValue)->get()->toArray();
        // $data = DB::table('npf_domains')->select('*')->where('subdomain',$request->hostname)->orWhere('subdomain','www.'.$request->hostname)->get()->toArray();
        $data = DB::table('np_structure_sites as nss')
                ->select('nss.*')
                ->join('np_structure_domains as nsd', 'nsd.site_id', '=', 'nss.id')
                ->where('nsd.fqdn',$request->hostname)
                ->orWhere('nsd.fqdn','www.'.$request->hostname)
                ->get()
                ->toArray();
        if(!empty($data)){
            if($data[0]->parent_id!=1){
                // $parentData = DB::table('npf_domains')->select('*')->where('id',$data[0]->parent_id)->get()->toArray();
                $parentData = DB::table('np_structure_sites')
                                ->select('*')
                                ->where('id',$data[0]->parent_id)
                                ->get()
                                ->toArray();
                if(!empty($parentData)) {
                    $data['parent_data'] = $parentData[0];
                    if ($parentData[0]->parent_id != 1) {
                        // $parent2Data = DB::table('npf_domains')->select('*')->where('id', $parentData[0]->parent_id)->get()->toArray();
                        $parent2Data = DB::table('np_structure_sites')
                                        ->select('*')
                                        ->where('id', $parentData[0]->parent_id)
                                        ->get()
                                        ->toArray();
                        if(!empty($parent2Data)) {
                            $data['parent2_data'] = $parent2Data[0];
                            if($parent2Data[0]->parent_id != 1) {
                                // $parent3Data = DB::table('npf_domains')->select('*')->where('id', $parent2Data[0]->parent_id)->get()->toArray();
                                $parent3Data = DB::table('np_structure_sites')
                                                ->select('*')
                                                ->where('id', $parent2Data[0]->parent_id)
                                                ->get()
                                                ->toArray();
                                if (!empty($parent3Data)) {
                                    $data['parent3_data'] = $parent3Data[0];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

}