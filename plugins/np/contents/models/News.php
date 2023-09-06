<?php namespace Np\Contents\Models;

/**
 * Model
 */
class News extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title', 'body'];
    
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];
    
    protected $guarded=[];
    protected $slugs = [

    ];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    public $jsonable = ['linkpicker1', 'linkpicker2'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_news';

    /**
     * @var array Validation rules
     */
    public $rules = ['title' => 'required', 'publish_date' => 'required', 'archive_date' => 'required'];

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

    //relation
    public $attachOne = [
        'image' => 'Np\Contents\Models\File',
        'attachment' => 'Np\Contents\Models\File'
    ];

    //attachments
    public $attachMany = [
        'images'=>'Np\Contents\Models\File',
        'attachments' => 'Np\Contents\Models\File'
    ];
}
