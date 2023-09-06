<?php namespace Np\Contents\Models;


/**
 * Model
 */
class DigitalfairEservice extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title' ];
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];

    protected $slugs = [

    ];
    
    //attachments
    public $attachMany = ['attachments'=>'Np\Contents\Models\File'];

    

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    //relation
    public $belongsTo = [
        'digitalfair_year_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'digitalfair_year'],
        'digitalfair_eservice_category_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'digitalfair_eservice_category']
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_digitalfair_eservice';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required', 'attachments'=>'required', 'digitalfair_year'=>'required', 'digitalfair_eservice_category'=>'required'];

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
}
