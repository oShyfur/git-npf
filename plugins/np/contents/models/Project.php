<?php

namespace Np\Contents\Models;


/**
 * Model
 */
class Project extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title', 'body', 'field_project_work_description', 'field_project_allotment_others', 'field_project_latest_status'];
    
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];

    protected $slugs = [

    ];

    //attachments
    public $attachMany = ['attachments' => 'Np\Contents\Models\File'];



    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_project';

    /**
     * @var array Validation rules
     */
    public $rules = ['title' => 'required', 'field_project_work_description' => 'required', 'field_project_time_duration_start' => 'required'];

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
    
    //Relation
    public $belongsTo = [
        'projects_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'projects'],
        'project_implemented_or_proposed_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'project_implemented_or_proposed']
    ];
}
