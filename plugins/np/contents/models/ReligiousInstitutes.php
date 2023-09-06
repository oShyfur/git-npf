<?php namespace Np\Contents\Models;


/**
 * Model
 */
class ReligiousInstitutes extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'field_history', 'field_head_person_name', 'field_head_person_designation', 'field_religious_ins_contact' ];
    
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
		'religious_institutes_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'religious_institutes']
	];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_religious_institutes';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','field_head_person_name'=>'required','field_head_person_designation'=>'required','field_head_person_mobile'=>'required','field_religious_ins_contact'=>'required','image'=>'required'];

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
