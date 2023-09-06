<?php

namespace Np\Contents\Models;

use Np\Contents\Traits\NpSortable;
use October\Rain\Database\Traits\Sortable;

/**
 * Model
 */
class Leaders extends NPContentsBaseModel
{

    use Sortable {
        Sortable::setSortableOrder as parentSetSortableOrder;
    }
    use NpSortable;


    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title', 'field_own_district', 'field_permanent_address', 'field_present_address'];
    
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

    public $belongsTo = [
        'designation_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'designation'],
        'field_highest_educational_degree_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'field_highest_educational_degree']
    ];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_leaders';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required', 
        'designation' => 'required', 
        // 'image' => 'required', 
        'field_mobile' => 'required'
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
}
