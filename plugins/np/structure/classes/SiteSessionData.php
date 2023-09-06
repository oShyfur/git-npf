<?php

namespace Np\Structure\Classes;

use BackendAuth;
use Illuminate\Support\Facades\Session;
use Np\Structure\Models\ContentType;
use Np\Structure\Models\TexonomyType;
use Np\Structure\Models\Block;
use Np\Structure\Models\LayerResource;
use System\Models\MailPartial;
use DB;
class SiteSessionData
{
    public static function setSite($site)
    {
        $cluster = $site->db->cluster;
        $clusterData['host'] = $cluster->host;
        $clusterData['username'] = $cluster->username;
        $clusterData['password'] = $cluster->password;

        Session::put('cluster', $clusterData);

        //filter ct based on user
        $loggedInUser = BackendAuth::getUser();

        $contentTypes = (!empty($loggedInUser->allowed_ct) and is_array($loggedInUser->allowed_ct)) ? $loggedInUser->allowed_ct : self::getResource($site, 'content_types');
        $taxonomies = self::getResource($site, 'taxonomies');
        $blocks = self::getResource($site, 'blocks');
        $forms = self::getResource($site, 'forms');

        $domains = $site->domains()->get(['id', 'fqdn'])->toArray();

        /// Added AS 10/2/2022
        $contentLastUpdate = self::contentLastUpdateGet($site);
        
        $siteData = [
            'id' => $site->id,
            'uuid' => $site->uuid,
            'name' => $site->name,
            'default_lang' => $site->site_default_lang ?: 'bn',
            'languages' => $site->getSiteLanguages(),
            'database' => $site->db_id,
            'domains' => $domains,
            'resources' => [
                'content_types' => ContentType::select('id', 'name', 'code', 'is_common', 'table_name', 'icon','frequency')->find($contentTypes)->toArray(),
                'taxonomy_types' => TexonomyType::select('id', 'name', 'is_common', 'sort_order', 'code')->find($taxonomies)->toArray(),
                'blocks' => Block::select('id', 'title', 'region', 'sort_order', 'type', 'partial_code')->find($blocks)->where('type', 1)->toArray(),
                'forms' => MailPartial::find($forms)->toArray(),
                'contentLastUpdate' => $contentLastUpdate,
            ]
        ];

        Session::put('site', $siteData);
    }
    //use for frequency update
    public static function contentLastUpdateGet($site){
        $cluster = $site->db->cluster;
        $clusterData['host'] = $cluster->host;
        $clusterData['username'] = $cluster->username;
        $clusterData['password'] = $cluster->password;

        Session::put('cluster', $clusterData);

        $loggedInUser = BackendAuth::getUser();

        $contentTypes = (!empty($loggedInUser->allowed_ct) and is_array($loggedInUser->allowed_ct)) ? $loggedInUser->allowed_ct : self::getResource($site, 'content_types');

        $contentLastUpdate = array();
        $contentTypesList = ContentType::select('id', 'name', 'code', 'is_common', 'table_name', 'icon','frequency')->find($contentTypes)->toArray();
        foreach ($contentTypesList as $ctl => $value) {
            $model = $value['table_name'];
            $key = 'database.connections.tenant';
            $db = config($key);
            $db['host'] = $cluster->host;
            $db['username'] = $cluster->username;
            $db['password'] = $cluster->password;
            $db['database'] = $site['db_id'];
            config(
                [$key => $db]
            );
            $item = new $model;
            $table = $item->getTable(); 
            $data = DB::connection('tenant')->table($table)->select('updated_at')->where('site_id',$site['id'])->orderBy('updated_at','desc')->first();
            $contentLastUpdate[$ctl]['id'] = $value['id'];
            $contentLastUpdate[$ctl]['name'] = $value['name'];
            $contentLastUpdate[$ctl]['tableName'] = $table;
            $contentLastUpdate[$ctl]['frequency'] = $value['frequency'];
            $contentLastUpdate[$ctl]['updated_at'] = ($data == null)?null:$data->updated_at;
            $contentLastUpdate[$ctl]['today_at'] = date('Y-m-d h:i:s');
            if($data != null){
                $today_time = strtotime(date('Y-m-d h:i:s'));
                $expire_time = strtotime($data->updated_at);
                  
                $diff_days = (int)floor(abs($expire_time - $today_time) /(60*24*60));

            }else{
                $diff_days = 0;
            }
            $contentLastUpdate[$ctl]['diff_days'] = ($diff_days > 0)?$diff_days:0 ;
            $contentLastUpdate[$ctl]['over_date'] = ($diff_days > $value['frequency'])?($diff_days - $value['frequency']):0 ;
            $contentLastUpdate[$ctl]['in_date']   = ($diff_days < $value['frequency'])?($value['frequency'] - $diff_days):0 ;
        }
        return $contentLastUpdate;

    }

    public static function updateContentData($table){
        $cluster = $site->db->cluster;
        $clusterData['host'] = $cluster->host;
        $clusterData['username'] = $cluster->username;
        $clusterData['password'] = $cluster->password;

        Session::put('cluster', $clusterData);
        $key = 'database.connections.tenant';
        $db = config($key);
        $db['host'] = $cluster->host;
        $db['username'] = $cluster->username;
        $db['password'] = $cluster->password;
        $db['database'] = $site['db_id'];
        config(
            [$key => $db]
        );

        

    }

    //end use for frequency code

    public static function getResource($site, $type)
    {
        $ministry = $site->ministry;
        // Merge site resource, layer resource & ministry layer resources
        $siteResources = $site->site_resource;
		logger($site->layer ?? "Not working");
        $layersResources = LayerResource::where('layer_id', $site->layer->id)->where('ministry_id', 0)->first();
        $ministryLayersResources = LayerResource::where('layer_id', $site->layer->id)
            ->when($ministry, function ($q) use ($ministry) {
                $q->where('ministry_id', $ministry->id);
            })->first();

        $siteResourcestype = (isset($siteResources->{$type}) and is_array($siteResources->{$type}))  ? $siteResources->{$type} : [];
        $layersResourcestype = (isset($layersResources->{$type}) and is_array($layersResources->{$type})) ? $layersResources->{$type} : [];
        $ministryLayersResourcestype = (isset($ministryLayersResources->{$type}) and is_array($ministryLayersResources->{$type})) ? $ministryLayersResources->{$type} : [];

        return array_unique(array_merge(
            $siteResourcestype,
            $layersResourcestype,
            $ministryLayersResourcestype
        ));
    }
}
