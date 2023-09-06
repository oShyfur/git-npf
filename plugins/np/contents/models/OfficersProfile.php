<?php namespace Np\Contents\Models;


/**
 * Model
 */
class OfficersProfile extends NPContentsBaseModel
{
	use \October\Rain\Database\Traits\Nullable;
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'name' ];
    
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'name'
    // ];

    protected $slugs = [

    ];

    protected $nullable = ["officer_category"] ;

    //attachments
    public $attachOne = ['attachment'=>'Np\Contents\Models\File','image'=>'Np\Contents\Models\File'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    public $jsonable = ['designation','officer_category'];

    public $belongsTo = [
        'designation_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'designation'],
        'officer_category_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'officer_category']
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_officers_profile';

    /**
     * @var array Validation rules
     */
    public $rules =['sort_order'=>'required','name'=>'required','designation'=>'required'];

    /**
     * Slug can be insert not update
     */
    public function beforeSave()
    {
        if (!empty($this->slug)) {
            $this->slugs = ['slug'=>'slug'];
        }else{
            unset($this->slug);
            $this->slugs = ['slug'=>'name'];
            $this->slugAttributes();
        }
    }
}
