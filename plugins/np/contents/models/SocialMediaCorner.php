<?php namespace Np\Contents\Models;


/**
 * Model
 */
class SocialMediaCorner extends NPContentsBaseModel
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

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

	public $belongsTo = [
		'social_link_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'social_link']
	];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_social_media_corner';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','field_social_media_url'=>'required','social_link'=>'required'];

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
