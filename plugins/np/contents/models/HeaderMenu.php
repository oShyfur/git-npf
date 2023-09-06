<?php namespace Np\Contents\Models;


/**
 * Model
 */
class HeaderMenu extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title' ];
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => ['title']
    // ];
    
    protected $slugs = [
        
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
            $this->slugs = ['slug'=>['title']];
            $this->slugAttributes();
        }
    }
    
    //jsonable
    public $jsonable = ['link'];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_header_menu';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','link'=>'required','sort_order'=>'required'];

    /**
     * @var array - id, parent id casting
     */
    protected $casts = [
        'id' => 'string',
        'parent_id' => 'string'
    ];

    public function scopeOnlyParent($query)
    {
        return $query->where('publish', 1)->whereNull('deleted_at')->whereNull('parent_id');
    }
}
