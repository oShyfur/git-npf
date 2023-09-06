<?php namespace Np\Contents\Models;

use Illuminate\Support\Facades\DB;
use Np\Contents\Scopes\SiteScope;

/**
 * Model
 */
class InnovationContent extends NPContentsBaseModel
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
    public $attachOne = ['attachment'=>'Np\Contents\Models\File'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];
	
	public $belongsTo = [
		'innovation_content_category_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'innovation_content_category']
	];
		
    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_innovation_content';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','innovation_content_category'=>'required'];

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
