<?php namespace Np\Contents\Models;


/**
 * Model
 */
class TcvDetails extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'tcv_about', 'bsd_about', 'bvs_about', 'bmds_about', 'bsc_about' ];
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
    
    public $attachOne = ['image'=>'Np\Contents\Models\File','image2'=>'Np\Contents\Models\File'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_tcv_details';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required'];
}
