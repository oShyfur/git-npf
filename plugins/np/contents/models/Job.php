<?php namespace Np\Contents\Models;


/**
 * Model
 */
class Job extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'overview', 'responsibility', 'qualification', 'benefit' ];
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

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    public $belongsTo = [
		'job_type_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'job_type']
	];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_jobs';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required', 'publish_date'=>'required', 'archive_date'=>'required', 'job_type'=>'required'];
}
