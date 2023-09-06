<?php namespace Np\Contents\Models;


/**
 * Model
 */
class TeamMember extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'name', 'body' ];
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'name'
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
            $this->slugs = ['slug'=>'name'];
            $this->slugAttributes();
        }
    }
    
    //attachments
    
    public $attachOne = ['image'=>'Np\Contents\Models\File'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_team_members';

    /**
     * @var array Validation rules
     */
    public $rules =[ 'name'=>'required'];

    //relations
    public $belongsTo = [
        'team_designation_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'team_designation'],
        'team_cluster' => ['Np\Contents\Models\TeamCluster', 'scope' => 'Status'],
        'team_name' => ['Np\Contents\Models\TeamName']
    ];

    public function getTeamNameOptions()
    {
        $team_name = [];
        if ($this->team_cluster) {
            $team_name = $this->team_cluster->team_name;
            return $team_name->pluck('title', 'id');
        }

        return $team_name;
    }

}
