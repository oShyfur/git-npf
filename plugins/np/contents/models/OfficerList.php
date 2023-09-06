<?php

namespace Np\Contents\Models;

use Illuminate\Support\Facades\Log;
use Np\Contents\Scopes\SiteScope;
use Np\Contents\Traits\NpSortable;
use Np\Structure\Models\Site;
use October\Rain\Database\Traits\Sortable;

/**
 * Model
 */
class OfficerList extends NPContentsBaseModel
{
    use Sortable {
        Sortable::setSortableOrder as parentSetSortableOrder;
    }
    use NpSortable;

    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title', 'field_own_district', 'designation'];
    
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => ['title']
    // ];

    protected $slugs = [

    ];

    //attachments

    public $attachOne = ['image' => 'Np\Contents\Models\File'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    public $belongsTo = [
        'section_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'section']
    ];

    protected $casts = [
        'field_batch' => 'integer',
        'id_number' => 'integer'
    ];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_officer_list';

    /**
     * @var array Validation rules
     */
    public $rules = [

        'title' => 'required',
        // 'image' => 'required',
        'phone_office' => 'required',
        'mobile' => 'required',
        'email' => 'required',
        'designation' => 'required',
    ];

    /**
     * Slug can be insert not update
     */
    public function beforeSave()
    {
        if (!empty($this->slug)) {
            $this->slugs = ['slug'=>'slug'];
        }else{
            unset($this->slug);
            $this->slugs = ['slug'=>'title'];
            $this->slugAttributes();
        }
    }


    public function setSortableOrder($itemIds, $itemOrders = null)
    {
        $this->parentSetSortableOrder($itemIds, $itemOrders);
        $this->clearCacheTag($this);
    }

    public function getSortableLabelAttribute()
    {
        return  $this->title . " (" . $this->designation . ")";
    }



    public function scopeActiveOfficers($query)
    {
        $query->orderBy('publish', 'desc')->orderBy('sort_order', 'asc');
    }

    public function scopeOneStepUpLevelOfficers($query)
    {
        // fetch current site
        $currentSiteId = $this->getCurrentSite('id');
        $currentSite = Site::find($currentSiteId);
        $layer = (int) $currentSite->layer_id;
        $ministry = $currentSite->ministry_id;

        // up level parent site in line ministry
        $layer = $layer - 1;
        $upperSite = Site::where('ministry_id', $ministry)->where('layer_id', $layer)->first();
        $siteId = $upperSite ? $upperSite->id : null;

        //remove site scope
        $query = $query->withoutGlobalScopes();

        // get upper level site's officers
        $query->where('site_id', $siteId)->orderBy('publish', 'desc')->orderBy('sort_order', 'asc');
    }
}
