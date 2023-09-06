<?php namespace Np\Contents\Models;


/**
 * Model
 */
class Tender extends NPContentsBaseModel
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
    
    //attachments
    public $attachMany = ['attachments'=>'Np\Contents\Models\File'];

    

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    public $belongsTo = [
		'tender_type_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'tender_type']
	];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_tenders';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required', 'publish_date'=>'required', 'archive_date'=>'required', 'tender_type'=>'required'];
}
