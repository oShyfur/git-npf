<?php namespace Np\Contents\Models;


/**
 * Model
 */
class FieldOffice extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
        //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'body', 'field_office_cism', 'field_citizen_charter', 'field_important_info', 'field_projects', 'field_address' ];
    // SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
    // protected $slugs = [
    //     'slug' => 'title'
    // ];

	protected $slugs = [

    ];
    
    //attachments
    
    public $attachOne = ['attachment'=>'Np\Contents\Models\File','image'=>'Np\Contents\Models\File'];

    //jsonable
    public $jsonable = ['field_process_maps_nid','field_e_directory_nid','field_staff_office_nid','field_notice_node_ref_nid','field_ref_gov_download_nid','field_law_circular_nid','field_photo_gallery_office_nid'];


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_field_office';
	
	public $belongsTo = [
		'field_info_officer_nid_taxonomy' => ['Np\Contents\Models\OfficerList', 'key' => 'field_info_officer_nid']
	];

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','name'=>'field_e_directory_nid','name'=>'field_address'];

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
	public static function gets($slug_key, $slug_value) {
		$fo = FieldOffice::where($slug_key, $slug_value)->where('publish', 1)->first();	
		if($fo) {			
			if($fo->field_law_circular_nid) {
				$ids = array_column($fo->field_law_circular_nid, 'law_policy');
				$fo->field_law_circular_nid = LawPolicy::whereIn('id', $ids)->get();
			}
			
			if($fo->field_process_maps_nid) {
				$ids = array_column($fo->field_process_maps_nid, 'office_process_map');
				$fo->field_process_maps_nid = OfficeProcessMap::whereIn('id', $ids)->get();
			}
			
			if($fo->field_staff_office_nid) {
				$ids = array_column($fo->field_staff_office_nid, 'staff_list');
				$fo->field_staff_office_nid = StaffList::whereIn('id', $ids)->with('image')->get();
			}
			
			if($fo->field_e_directory_nid) {
				$ids = array_column($fo->field_e_directory_nid, 'officer_list');
				$fo->field_e_directory_nid = OfficerList::whereIn('id', $ids)->with('image')->get();
			}
			
			if($fo->field_ref_gov_download_nid) {
				$ids = array_column($fo->field_ref_gov_download_nid, 'files');
				$fo->field_ref_gov_download_nid = Files::whereIn('id', $ids)->with('attachments')->get();
			}
			
			if($fo->field_photo_gallery_office_nid) {
				$ids = array_column($fo->field_photo_gallery_office_nid, 'photogallery');
				$fo->field_photo_gallery_office_nid = PhotoGallery::whereIn('id', $ids)->with('images')->get();
			}
			
			if($fo->field_notice_node_ref_nid) {
				$ids = array_column($fo->field_notice_node_ref_nid, 'notices');
				$fo->field_notice_node_ref_nid = Notices::whereIn('id', $ids)->get();
			}
		
		}
		
		return $fo;
	}
	
}
