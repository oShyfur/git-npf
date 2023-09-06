<?php

namespace Np\Contents\Models;


/**
 * Model
 */
class InfoOfficer extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title'];
    
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
        'officer' => ['Np\Contents\Models\OfficerList', 'key' => 'do', 'scope' => 'withoutSiteScope'],
        'alt_officer' => ['Np\Contents\Models\OfficerList', 'key' => 'ao', 'scope' => 'withoutSiteScope'],
        'appeal_authority' => ['Np\Contents\Models\OfficerList', 'key' => 'aa', 'scope' => 'withoutSiteScope']
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_info_officer';

    /**
     * @var array Validation rules
     */
    public $rules = ['title' => 'required'];

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

    // Custom Function
    public static function gets()
    {
        return InfoOfficer::where('id', '<>', -1);
    }
}
