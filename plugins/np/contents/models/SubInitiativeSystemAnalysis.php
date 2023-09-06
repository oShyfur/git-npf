<?php namespace Np\Contents\Models;


/**
 * Model
 */
class SubInitiativeSystemAnalysis extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'system_analysis' ];
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
    public $table = 'np_contents_sub_initiative_system_analysis';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','system_analysis'=>'required','initiative_category_id'=>'required','initiative_sub_category_id'=>'required'];

    //relations
    public $belongsTo = [
        'initiative_category' => ['Np\Contents\Models\InitiativeCategory', 'scope' => 'Status'],
        'initiative_sub_category' => ['Np\Contents\Models\InitiativeSubCategory']
    ];

    public function getInitiativeSubCategoryOptions()
    {
        $initiative_sub_category = [];
        if ($this->initiative_category) {
            $initiative_sub_category = $this->initiative_category->initiative_sub_category;
            return $initiative_sub_category->pluck('title', 'id');
        }

        return $initiative_sub_category;
    }
}
