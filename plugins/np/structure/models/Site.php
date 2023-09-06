<?php

namespace Np\Structure\Models;

use Illuminate\Support\Facades\Storage;
use Backend\Facades\BackendAuth;
use October\Rain\Database\Builder;
use Np\Structure\Classes\NP;
use Illuminate\Support\Facades\DB;
use Np\Structure\Classes\DatabaseConnection;
use Np\Structure\Classes\SiteSessionData;
use Np\Structure\Facades\Oisf;
use October\Rain\Exception\SystemException;
use October\Rain\Support\Facades\Schema;
use RainLab\Translate\Models\Locale;
use System\Models\MailLayout;

/**
 * Model
 */
class Site extends NPBaseModel
{

    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Purgeable;
    use \October\Rain\Database\Traits\SimpleTree;

    use \Np\Contents\Traits\SiteUpdateDataPutCentralDB;

    // for updated_at content update last date
    public $connection = 'mysql';

    public $implement = [
        'RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'name', 'site_title_line1', 'site_title_line2'
    ];

    protected $dates = ['deleted_at'];
    protected $jsonable = ['site_meta'];
    protected $purgeable = ['is_run_migration'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_sites';
    protected $guarded = ['uuid'];
    protected $run_migration = false;
    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'layer' => 'required',
        'site_theme_code' => 'required',
        'site_default_lang' => 'Required',
        'cluster_id' => 'required',
        'db_id' => 'required',
        'site_title_line1' => 'required',
        'ministry_id' => 'required',
        'directorate_id' => 'required',
    ];

    //relations

    public $belongsTo = [
        // 'layer' => ['Np\Structure\Models\Layer', 'scope' => 'onlyParent'],
        'layer' => ['Np\Structure\Models\Layer', 'scope' => 'Status'],
        'db' => ['Np\Structure\Models\DB'],
        'cluster' => ['Np\Structure\Models\Cluster'],
        'ministry' => ['Np\Structure\Models\Ministry'],
        'directorate' => ['Np\Structure\Models\Directorate'],
        'site_type' => ['Np\Structure\Models\SiteType', 'scope' => 'Status']
    ];

    public $belongsToMany = [
        'users' => [
            'Backend\Models\User',
            'table' => 'np_structure_site_user',
        ],
        'tags' => [
            'Np\Structure\Models\Tag',
            'table' => 'np_structure_site_tag',
        ],

        'languages' => [
            'RainLab\Translate\Models\Locale',
            'table' => 'np_structure_site_locale',
        ],
    ];

    public $hasMany = [
        'domains' => ['Np\Structure\Models\Domain']
    ];

    public $hasOne = [
        'site_resource' => ['Np\Structure\Models\SiteResource'],
        'domain' => ['Np\Structure\Models\Domain']
    ];

    public $attachOne = [
        'logo' => 'Np\Structure\Models\File'
    ];

    //functions

    public function getSiteTheme()
    {
        return $this->site_theme_code ? $this->site_theme_code : NP::defaultTheme();
    }
    public function getSiteLang($requestedLang)
    {

        $languages = array_keys($this->getSiteLanguages());

        return in_array($requestedLang, $languages) ? $requestedLang : $this->getSiteDefaultLang();
    }

    public function getSiteDefaultLang()
    {
        return $this->site_default_lang ? $this->site_default_lang : 'bn';
    }
    public function getSiteLanguages()
    {
        $defaults = NP::defaultLocales();

        $languages = $this->languages->pluck('name', 'code')->all();

        return array_merge($defaults, $languages);
    }

    public function getSiteDefaultLangOptions()
    {
        return Locale::listAvailableNew();
    }


    public function getSiteThemeCodeOptions()
    {
        return MailLayout::where('code', 'like', 'theme-%')->pluck('name', 'code')->all();
    }

    public function getParentOptions()
    {
        return DB::table('np_structure_sites')->whereIn('site_type_id', [4, 5, 6])->pluck('name', 'id')->all();
        // return MailLayout::where('code', 'like', 'theme-%')->pluck('name', 'code')->all();
    }


    public function getDivisionalHierarchyIdOptions()
    {
        // $result = Site::where('site_type_id', 14)
        //     ->where('layer_id', 4)
        //     ->pluck('name', 'id');
        $result = Site::with('domain')
            ->where('site_type_id', 14)
            ->where('layer_id', 4)
            ->select('id', 'name')
            ->get()
            ->map(function ($item) {
                $item->name_domain = $item->name . ' [' . ($item->domain ? $item->domain->fqdn.']' : '' );
                unset($item->name, $item->domain);
                return $item;
            })
            ->pluck('name_domain', 'id')
            ->all();

        return $result;
    }

    public function getDistrictHierarchyIdOptions()
    {
        // $result = Site::where('site_type_id', 14)
        //     ->where('layer_id', 5)
        //     ->pluck('name', 'id');
        $result = Site::with('domain')
        ->where('site_type_id', 14)
        ->where('layer_id', 5)
        ->select('id', 'name')
        ->get()
        ->map(function ($item) {
            $item->name_domain = $item->name . ' [' . ($item->domain ? $item->domain->fqdn.']' : '' );
            unset($item->name, $item->domain);
            return $item;
        })
        ->pluck('name_domain', 'id')
        ->all();

        return $result;
    }

    public function getLogoUrlAttribute()
    {
        $defaultLogo = env('CDN_PUBLIC_URL', 'http://file.portal.gov.bd') . '/media/central/themes/' . $this->getSiteTheme() . '/img/logo.png';

        return $this->logo ? $this->logo->path : $defaultLogo;
    }

    //events

    public function beforeCreate()
    {
        $this->uuid = $this->generate_uuid();
    }

    public function afterCreate()
    {
        // create tenant folders
        $path = 'media/' . $this->uuid;
        Storage::disk('local')->makeDirectory($path);

        $path = 'uploads/' . $this->uuid;
        Storage::disk('local')->makeDirectory($path);

        //copy layered blocks to tenant bd
        $this->copyBlocksToTenant();
    }

    public function copyBlocksToTenant()
    {
        $blocks = SiteSessionData::getResource($this, 'blocks');

        $cluster = $this->cluster;
        $db = $this->db_id;
        $connection = DatabaseConnection::setConnection($cluster, $db);
        $tmpSqlPath = storage_path('temp') . '/ct.sql';


        DatabaseConnection::migrateBlocks($blocks, $this->id, $connection, $tmpSqlPath);
        $this->executeSqlFile($connection, $tmpSqlPath);
    }

    public function beforeSave()
    {
        $this->level_id = $this->level_id ?: 0;
        $this->oisf_office_id = $this->oisf_office_id ?: 0;
    }
    public function afterSave()
    {
        // run migration
        if ($this->db_id) {
            $this->runMigration();
        }
    }


    // Scopes
    public function scopeContextLayers($query)
    {
        $layerId = $this->layer_id;
        return Layer::where('id', '>=', $layerId)->get()->toArray();
    }
    /**
     * Query scope which extracts a certain node object from the current query expression.
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeWithoutNode($query, $node)
    {
        return $query->where($node->getKeyName(), '!=', $node->getKey());
    }

    /**
     * Extracts current node (self) from current query expression.
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeWithoutSelf($query)
    {
        return $this->scopeWithoutNode($query, $this);
    }

    /**
     * Set of all children & nested children.
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAllChildren($query, $geoLayer = 0, $includeSelf = false)
    {
        $layer = $this->layer;
        //$geo = $this->getGeo($layer, $domain);

        $query
            ->where($this->getLeftColumnName(), '>=', $this->getLeft())
            ->where($this->getLeftColumnName(), '<', $this->getRight());

        return $includeSelf ? $query : $query->withoutSelf();
    }

    /**
     * Returns a prepared query with all parents up the tree.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParents($query, $includeSelf = false)
    {
        $query
            ->where($this->getLeftColumnName(), '<=', $this->getLeft())
            ->where($this->getRightColumnName(), '>=', $this->getRight());

        return $includeSelf ? $query : $query->withoutSelf();
    }

    /**
     * Filter targeting all children of the parent, except self.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSiblings($query, $includeSelf = false)
    {
        $query->where($this->getParentColumnName(), $this->getParentId());

        return $includeSelf ? $query : $query->withoutSelf();
    }

    /**
     * Returns all final nodes without children.
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLeaves($query)
    {
        $grammar = $this->getConnection()->getQueryGrammar();

        $rightCol = $grammar->wrap($this->getQualifiedRightColumnName());
        $leftCol = $grammar->wrap($this->getQualifiedLeftColumnName());

        return $query
            ->allChildren()
            ->whereRaw($rightCol . ' - ' . $leftCol . ' = 1');
    }

    // csutom functions


    public function getGeo($layer, $domain)
    {
    }


    //oisf sso

    public function getOisfMinistryOptions()
    {
        $items = [];

        if ($this->name)
            $items = collect(Oisf::getOisfOfficeMinistry())->pluck('name', 'id')->toArray();

        return $items;
    }

    public function getOisfLayerOptions()
    {
        $items = [];

        if ($this->_oisf_ministry)
            $items = collect(Oisf::getOisfOfficeLayer($this->_oisf_ministry))->pluck('name', 'id')->toArray();

        return $items;
    }

    public function getOfficeOptions()
    {
        $items = [];

        if ($this->_oisf_layer)
            $items = collect(Oisf::getOisfOffice($this->_oisf_ministry, $this->_oisf_layer))->pluck('name', 'id')->toArray();

        return $items;
    }


    public function getResources($type)
    {
        $resources = $this->site_resource;
        // Merge site resource, layer resource & ministry layer resources
        $layersResources = LayerResource::where('layer_id', $this->layer->id)->first();
        $ministryLayersResources = LayerResource::where('layer_id', $this->layer->id)->where('ministry_id', $this->ministry->id)->first();

        $contentTypes = $resources->content_types;
    }
    public function getDbOptions()
    {
        $dbs = [];
        if ($this->cluster) {
            $dbs = $this->cluster->dbs;
            return $dbs->pluck('name', 'id');
        }

        return $dbs;
    }

    public function getLevelIdOptions()
    {
        $levels = [];
        if ($this->layer) {
            return Layer::where('parent_id', $this->layer->id)->pluck('name', 'id');
        }

        return $levels;
    }

    protected function getSql($tableName, $connection)
    {
        if (!Schema::connection('createnewdb')->hasTable($tableName)) {
            $sql = 'SHOW CREATE TABLE ' . $tableName;
            $results = collect(DB::select(DB::raw($sql)));
            return $results->first()->{'Create Table'} . ';';
        }
        return '';
    }
    protected function runTableMigration($connection, $tmpSqlPath)
    {

        if (isset($this->site_resource->content_types) and is_array($this->site_resource->content_types)) {


            $content_types = array_map(function ($val) {
                return (int) $val;
            }, $this->site_resource->content_types);

            DatabaseConnection::migrateCT($content_types, $connection, $tmpSqlPath);
        }
    }
    protected function runTaxonomyMigration($connection, $tmpSqlPath)
    {
        if (isset($this->site_resource->taxonomies) and is_array($this->site_resource->taxonomies)) {
            $taxonomies = array_map(function ($val) {
                return (int) $val;
            }, $this->site_resource->taxonomies);

            DatabaseConnection::migrateTT($taxonomies, $connection, $tmpSqlPath);
        }
    }
    protected function runBlockMigration($connection, $tmpSqlPath)
    {
        if (isset($this->site_resource->blocks) and is_array($this->site_resource->blocks)) {

            $itemIds = array_map(function ($val) {
                return (int) $val;
            }, $this->site_resource->blocks);

            DatabaseConnection::migrateBlocks($itemIds, $this->id, $connection, $tmpSqlPath);
        }
    }
    protected function runMigration()
    {

        $cluster = $this->cluster;
        $db = $this->db_id;
        $connection = DatabaseConnection::setConnection($cluster, $db);
        $tmpSqlPath = storage_path('temp') . '/ct.sql';

        //table migration
        $this->runTableMigration($connection, $tmpSqlPath);
        $this->runTaxonomyMigration($connection, $tmpSqlPath);
        $this->runBlockMigration($connection, $tmpSqlPath);
        //$this->runViewMigration($connection, $tmpSqlPath);

        $this->executeSqlFile($connection, $tmpSqlPath);
    }

    protected function executeSqlFile($connection, $tmpSqlPath)
    {
        if (file_exists($tmpSqlPath)) {

            $connection->beginTransaction();
            try {
                $connection->unprepared(file_get_contents($tmpSqlPath));
                $connection->commit();
            } catch (\Exception $e) {
                //dd($e->getMessage());
                throw new SystemException($e->getMessage());

                $connection->rollback();
            }

            unlink($tmpSqlPath);
        }
    }

    public function siteLists()
    {
        $loggedInuser = BackendAuth::getUser();

        $currentSites = $loggedInuser->sites->pluck('id')->all();
        $currentSitesTypeID = $loggedInuser->sites->pluck('site_type_id')->all();
        $sites = [];

        $siteQuery = Site::query();
        if ($loggedInuser->adminLevelUser()) {
            $sites = $siteQuery->pluck('name', 'id')->all();
        } else {
            $sites = $this->getSiteDetailsParentChildLayer($currentSites, $currentSitesTypeID);
        }

        // if ($loggedInuser->adminLevelUser()){
        //     $sites = $siteQuery->pluck('name', 'id')->all();
        // } elseif ($currentSitesTypeID[0] == 6){
        //     $sites = $siteQuery->whereIn('parent_id', $currentSites)->pluck('name', 'id')->all();
        // } else{
        //     $sites = $siteQuery->whereIn('parent_id', $currentSites)->where('site_type_id', 14)->pluck('name', 'id')->all();
        // }
        // // logger($sites);
        return $sites;
    }

    public function getSiteDetailsParentChildLayer($site, $type)
    {
        if ($type[0] == 4) {
            $firstLayer  = Site::whereIn('parent_id', $site)->where('site_type_id', '!=', 14)->where('site_type_id', '!=', 4)->pluck('id')->all();
            $zilla_gov  = Site::whereIn('parent_id', $site)->where('site_type_id', '!=', 4)->pluck('name', 'id')->all();
            $up = Site::whereIn('parent_id', $firstLayer)->where('site_type_id', '!=', 14)->pluck('name', 'id')->all();
            $array = $zilla_gov + $up; //array_merge_recursive($zilla_gov,$up);
            return $array;
            // $thirdLayer  = Site::whereIn('parent_id', $secondLayer)->where('site_type_id','!=', 14)->pluck('id')->all();
        } elseif ($type[0] == 5) {
            $firstLayer  = Site::whereIn('parent_id', $site)->where('site_type_id', '!=', 14)->pluck('id')->all();
            $upzilla_zillagov  = Site::whereIn('parent_id', $site)->pluck('name', 'id')->all();
            $union  = Site::whereIn('parent_id', $firstLayer)->where('site_type_id', '!=', 14)->pluck('name', 'id')->all();
            $array = $upzilla_zillagov + $union;
            return $array;
        } elseif ($type[0] == 6) {
            $union_upzillagov  = Site::whereIn('parent_id', $site)->pluck('name', 'id')->all();
            return $union_upzillagov;
        } elseif ($type[0] == 14) {
            $doictDomain  = DB::table('np_structure_domains')->whereIn('site_id', $site)->first();
            $fqdn = $doictDomain->fqdn;
            $domainSlice = explode('.', $fqdn);
            $domainCode  = $domainSlice[0];
            if ($domainCode == "doict") {
                $doictSiteId  = $doictDomain->site_id;
                $doictParent  = Site::select('parent_id')->where('id', $doictSiteId)->first();
                $doict_access = Site::where('parent_id', $doictParent->parent_id)->pluck('name', 'id')->all();
                return $doict_access;
            } else {
                $array = array();
                return $array;
            }
        } else {
            $array = array();
            return $array;
        }
    }
    public function scopeFilterByLoggedInUser($query)
    {
        $loggedInuser = BackendAuth::getUser();

        if (!$loggedInuser->adminLevelUser()) {
            return $query->whereHas('users', function (Builder $query) use ($loggedInuser) {
                $query->where('id', $loggedInuser->id);
            });
        }
        return $query;
    }

    public function getSiteResources($type = 'all', $resources_ids)
    {

        $resources_ids = array_map(function ($val) {
            return (int) $val;
        }, $resources_ids);

        switch ($type) {
            case 'content-type':
                return ContentType::whereIN('id', $resources_ids)->orWhere('is_common', 1)->get();
                break;

            case 'taxonomy-type':
                return TexonomyType::whereIN('id', $resources_ids)->orWhere('is_common', 1)->get();
                break;
        }

        return $this->site_resource;
    }
}
