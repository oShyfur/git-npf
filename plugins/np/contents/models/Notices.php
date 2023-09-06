<?php

namespace Np\Contents\Models;

use Backend\Facades\BackendAuth;

/**
 * Model
 */
class Notices extends NPContentsBaseModel
{

    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    
    
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'title', ['slug', 'index' => true], 'body'
    ];

    protected $guarded=[];
    
    // protected $slugs = ['slug' => 'title'];

    protected $slugs = [

    ];

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_notices';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        // 'attachments' => 'required',
        'publish_date' => 'required',
        'archive_date' => 'required'
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


    //relation
    public $attachOne = [
        'image' => 'Np\Contents\Models\File'
    ];

    //attachments
    public $attachMany = ['attachments' => 'Np\Contents\Models\File'];
}
