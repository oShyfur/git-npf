<?php namespace Np\Contents\Models;


/**
 * Model
 */
class Hospitalclinic extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'field_hospitalclinic_address', 'field_hospitalclinic_serv_list', 'field_contact_person_name', 'field_contact_person_designation' ];
    
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];

    protected $slugs = [

    ];
    
    //attachments
    public $attachMany = ['images'=>'Np\Contents\Models\File'];

    public $attachOne = ['image'=>'Np\Contents\Models\File'];

    public $belongsTo = [
        'field_hospitalclinic_type_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'field_hospitalclinic_type']
    ];
    
    //jsonable
    public $jsonable = ['field_hospitalclinic_doctor_nid'];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_hospitalclinic';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','field_hospitalclinic_type'=>'required'];

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
