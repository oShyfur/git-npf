<?php namespace Np\Contents\Models;


/**
 * Model
 */
class TeamCluster extends NPContentsBaseModel
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



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_team_clusters';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required'];

    // relations
    public $hasMany = [
        'team_name' => ['Np\Contents\Models\TeamName'],
        'team_member' => ['Np\Contents\Models\TeamMember']
    ];

    // Define Scope
    public function scopeStatus($query)
    {
        $query->where('publish', 1)->whereNull('deleted_at')->orderBy('sort_order');
    }
}
