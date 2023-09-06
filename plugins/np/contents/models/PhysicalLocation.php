<?php namespace Np\Contents\Models;


/**
 * Model
 */
class PhysicalLocation extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['address'];
    // SLuggable
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'address'
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
            $this->slugs = ['slug'=>'address'];
            $this->slugAttributes();
        }
    }

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_physical_locations';

    /**
     * @var array Validation rules
     */
    public $rules =[
        'address'=>'required',
    ];
}
