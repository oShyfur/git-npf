<?php

namespace Np\Contents\Models;


/**
 * Model
 */
class ServiceBoxCategory extends NPContentsBaseModel
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

    //attachments

    public $attachOne = ['image' => 'Np\Contents\Models\File'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_service_box_category';

    /**
     * @var array Validation rules
     */
    public $rules = ['title' => 'required', 'image' => 'required|image', 'sort_order' => 'required'];

    public $hasMany = [
        'items' => ['Np\Contents\Models\ServiceBoxItems']
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

    //functions

    public static function getCategoryWithItems($limit = 8)
    {
        return self::with(['items' => function ($q) {
            return $q->orderBy('is_pin', 'desc')->orderBy('created_at', 'desc');
        }])->orderBy('sort_order', 'asc')->get()->map(function ($query) use ($limit) {

            $query->setRelation('items', $query->items->take($limit));
            return $query;
        });
    }
}
