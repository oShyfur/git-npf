<?php

namespace Np\Contents\Models;

use Np\Contents\Traits\NpSortable;
use October\Rain\Database\Traits\Sortable;

/**
 * Model
 */
class StaffList extends NPContentsBaseModel
{
    use Sortable {
        Sortable::setSortableOrder as parentSetSortableOrder;
    }
    use NpSortable;

    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title', 'designation'];
    
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



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_staff_list';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required', 
        // 'image' => 'required', 
        'mobile' => 'required', 
        'designation' => 'required'
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
