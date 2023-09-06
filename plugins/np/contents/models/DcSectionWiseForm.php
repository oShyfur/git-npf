<?php namespace Np\Contents\Models;


/**
 * Model
 */
class DcSectionWiseForm extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'body' ];
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
	
	public $belongsTo = [
		'section_divcom_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'section_divcom'],
		'section_dcoffice_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'section_dcoffice']
	];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_dc_section_wise_form';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','attachments'=>'required'];

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
