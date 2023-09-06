<?php namespace Np\Contents\Models;


/**
 * Model
 */
class InitiativeCategory extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'slogan'];
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
    
    public $attachOne = ['image'=>'Np\Contents\Models\File'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_initiative_categories';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required'];

    // relations
    public $hasMany = [
        'initiative_sub_category' => ['Np\Contents\Models\InitiativeSubCategory'],
        'initiative_about' => ['Np\Contents\Models\InitiativeAbout'],
        'initiative_tcv' => ['Np\Contents\Models\InitiativeTcv'],
        'sub_initiative_partner' => ['Np\Contents\Models\SubInitiativePartner'],
        'sub_initiative_challenge' => ['Np\Contents\Models\SubInitiativeChallenge'],
        'sub_initiative_about' => ['Np\Contents\Models\SubInitiativeAbout'],
        'sub_initiative_key_impact' => ['Np\Contents\Models\SubInitiativeKeyImpact'],
        'sub_initiative_system_analysis' => ['Np\Contents\Models\SubInitiativeSystemAnalysis'],
        'sub_initiative_tcv' => ['Np\Contents\Models\SubInitiativeTcv'],
        'sub_initiative_award' => ['Np\Contents\Models\SubInitiativeAward'],
        'sub_initiative_publication' => ['Np\Contents\Models\SubInitiativePublication'],
        'sub_initiative_case_study' => ['Np\Contents\Models\SubInitiativeCaseStudy'],
        'sub_initiative_story' => ['Np\Contents\Models\SubInitiativeStory'],
        'sub_initiative_blog' => ['Np\Contents\Models\SubInitiativeBlog']
    ];

    // Define Scope
    public function scopeStatus($query)
    {
        $query->where('publish', 1)->whereNull('deleted_at')->orderBy('sort_order');
    }
}
