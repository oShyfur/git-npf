<?php namespace Np\Contents\Models;


/**
 * Model
 */
class BankFinancialOrg extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'field_bank_finance_branch_name', 'field_bank_branch_address', 'field_bank_atm_location', 'field_head_name', 'field_head_designation', 'field_contact_name', 'field_contact_designation' ];
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
		'category_of_bank_financial_org_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'category_of_bank_financial_org']
	];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_bank_financial_org';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','category_of_bank_financial_org'=>'required','field_bank_branch_address'=>'required'];

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
