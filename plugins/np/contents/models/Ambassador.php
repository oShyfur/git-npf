<?php namespace Np\Contents\Models;


/**
 * Model
 */
class Ambassador extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'block_title', 'name' ];
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'name'
    // ];

    protected $slugs = [

    ];
    
    //attachments
    
    public $attachOne = ['image'=>'Np\Contents\Models\File'];

    
    //jsonable
    public $jsonable = ['profile_link'];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_ambassador';

    /**
     * @var array Validation rules
     */
    public $rules =['block_title'=>'required','name'=>'required'];

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
