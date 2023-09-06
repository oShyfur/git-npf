<?php namespace Np\Contents\Models;


/**
 * Model
 */
class TeamName extends NPContentsBaseModel
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
    public $table = 'np_contents_team_names';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','team_cluster_id'=>'required'];

    //relations
    public $belongsTo = [
        'team_cluster' => ['Np\Contents\Models\TeamCluster', 'scope' => 'Status']
    ];

    public $hasMany = [
        'team_member' => ['Np\Contents\Models\TeamMember']
    ];

}
