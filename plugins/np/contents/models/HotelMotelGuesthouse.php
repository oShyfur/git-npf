<?php namespace Np\Contents\Models;


/**
 * Model
 */
class HotelMotelGuesthouse extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'field_hotel_proprietor_name', 'field_contact_person_name', 'field_contact_person_designation', 'field_hotel_address', 'field_hotel_place', 'field_room_description' ];
    
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];

    protected $slugs = [

    ];
    
    //attachments
    public $attachMany = ['images'=>'Np\Contents\Models\File'];

    public $attachOne = ['image'=>'Np\Contents\Models\File'];
	
	public $belongsTo = [
		'institute_govt_nongovt_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'institute_govt_nongovt']
	];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_hotel_motel_guesthouse';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','institute_govt_nongovt'=>'required'];

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
