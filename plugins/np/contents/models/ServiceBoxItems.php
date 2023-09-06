<?php

namespace Np\Contents\Models;


/**
 * Model
 */
class ServiceBoxItems extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title'];
    
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];

    protected $slugs = [

    ];

    //jsonable
    public $jsonable = ['link'];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    public $belongsTo = [
        'service_box_category' => 'Np\Contents\Models\ServiceBoxCategory'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_service_box_items';

    /**
     * @var array Validation rules
     */
    public $rules = ['title' => 'required', 'link' => 'required', 'service_box_category_id' => 'required'];

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

    public function getServiceBoxCategoryIdOptions()
    {
        return ServiceBoxCategory::get()->pluck('title', 'id');
    }
}
