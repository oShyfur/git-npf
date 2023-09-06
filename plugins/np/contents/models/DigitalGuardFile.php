<?php namespace Np\Contents\Models;


/**
 * Model
 */
class DigitalGuardFile extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title', 'body', 'field_digital_guard', 'field_memo_no'];
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

    

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    //relation
    public $belongsTo = [
        'guard_file' => ['Np\Contents\Models\Files','key' => 'field_digital_guard_file_nid','scope' => 'withoutSiteScope'],
        'digital_guard_file_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'digital_guard_file'],
        'section_dcoffice_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'section_dcoffice'],
        'section_divcom_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'section_divcom']

    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_digital_guard_file';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','field_memo_no'=>'required'];

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
