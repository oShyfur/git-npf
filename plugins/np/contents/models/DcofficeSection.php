<?php namespace Np\Contents\Models;


/**
 * Model
 */
class DcofficeSection extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'body', 'field_contact', 'field_dc_section_citizen_service', 'field_dc_section_current_project', 'field_dc_section_duties', 'field_dc_section_others' ];
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];

    protected $slugs = [

    ];
    
    //attachments
    
    public $attachOne = ['image'=>'Np\Contents\Models\File'];

    
    //jsonable
    public $jsonable = ['field_dcsection_forms_nid','field_law_policy_nid','field_meeting_nid','field_protibedon_nid','field_staff_office_nid'];

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_dcoffice_section';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required'];

    public $belongsTo = [
        'officer' => ['Np\Contents\Models\OfficerList', 'key' => 'field_section_oc_nid'],
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


}
