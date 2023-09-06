<?php namespace Np\Contents\Models;


/**
 * Model
 */
class Adc extends NPContentsBaseModel
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
    
    //jsonable
    public $jsonable = ['field_dc_office_section_nid'];

    public $belongsTo = [
        'adc_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'adc'],
        'officer' => ['Np\Contents\Models\OfficerList', 'key' => 'field_e_directory_nid']
    ];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_adc';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required'];

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
