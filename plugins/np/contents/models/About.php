<?php namespace Np\Contents\Models;


/**
 * Model
 */
class About extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'body', 'slogan', 'strategy_details', 'strategy_points', 'simplify_slogan', 'simplify_details', 'model_change_details', 'model_change_points', 'initiative_slogan', 'initiative_details' ];
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
    
    public $attachOne = [
        'image'=>'Np\Contents\Models\File',
        'image1'=>'Np\Contents\Models\File',
        'image2'=>'Np\Contents\Models\File'
    ];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_about';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','body'=>'required','slogan'=>'required','simplify_slogan'=>'required','simplify_details'=>'required','model_change_details'=>'required','model_change_points'=>'required','initiative_slogan'=>'required','initiative_details'=>'required'];
}
