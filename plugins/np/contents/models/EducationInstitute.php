<?php

namespace Np\Contents\Models;


/**
 * Model
 */
class EducationInstitute extends NPContentsBaseModel
{
	use \October\Rain\Database\Traits\Validation;
	//Translatable
	public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
	public $translatable = ['title', 'body', 'field_edu_achievements', 'field_edu_future_plan', 'field_edu_outstanding_students', 'field_edu_scholarship_info', 'field_history', 'field_student_class', 'field_managing_committee', 'field_last_five_yrs_result', 'field_contact', 'field_description'];
	// SLuggable 
    // use \October\Rain\Database\Traits\Sluggable;
    use \Np\Contents\Traits\Sluggable;
	// protected $slugs = [
	// 	'slug' => 'title'
	// ];

	protected $slugs = [

    ];

	//attachments

	public $attachOne = ['image' => 'Np\Contents\Models\File'];
	public $belongsTo = [
		'institute_type_taxonomy' => ['Np\Contents\Models\Taxonomy', 'key' => 'institute_type'],
		'head_master' => [
			'Np\Contents\Models\Teachers',
			'key' => 'field_e_directory_headmaster_principal_nid',
			'scope' => 'withoutSiteScope'
		]
	];


	//jsonable
	public $jsonable = ['field_e_directory_teachers_profile_nid'];

	use \October\Rain\Database\Traits\SoftDelete;

	protected $dates = ['deleted_at'];



	/**
	 * @var string The database table used by the model.
	 */
	public $table = 'np_contents_education_institute';

	/**
	 * @var array Validation rules
	 */
	public $rules = ['title' => 'required'];

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

	//custom functions

	public static function getInstitutesWithCount()
	{
		$list =  EducationInstitute::where('publish', 1)
			->with('institute_type_taxonomy')
			->select('institute_type', \Illuminate\Support\Facades\DB::raw('COUNT(id) as total'))
			->groupBy('institute_type')
			->get();

		trace_log($list->toArray());
		return $list;
	}
	public static function gets($type = null)
	{
		$type = \Illuminate\Support\Facades\Input::get('type');

		$types = \Np\Contents\Models\Taxonomy::where('texonomy_type_id', 1)->get();
		$data = ['type' => $type, 'types' => $types];


		if (!isset($type)) {
			$d = EducationInstitute::where('publish', 1)
				->with('institute_type_taxonomy')
				->with('head_master')
				->select('institute_type', \Illuminate\Support\Facades\DB::raw('COUNT(id) as total'))
				->groupBy('institute_type')
				->get();
			$data['data'] = $d;
		} else {
			$d = EducationInstitute::where('publish', 1)
				->where('institute_type', $type)
				->with('institute_type_taxonomy')
				->with('head_master')
				->get();
			$data['data'] = $d;
			if ($d && count($d) > 0) {
				$title = $d[0]['institute_type_taxonomy']['name'];
				$data['title'] = $title;
			}
		}

		trace_log($data);
		return $data;
	}
}
