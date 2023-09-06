<?php namespace Np\Contents\Models;

use Np\Contents\Models\RevenueModelJob;
use \Illuminate\Support\Facades\DB;
/**
 * Model
 */
class RevenueModelAdvertisement extends NPContentsBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [ 'title', 'body' ];
    
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


    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_revenue_model_advertisement';

    /**
     * @var array Validation rules
     */
    public $rules =['title'=>'required','publish_date'=>'required','archive_date'=>'required'];

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
	public static function getJobTenderAdData() {
		$job_count = RevenueModelJob::where('publish', 1)->get()->count();
		$tender_count = RevenueModelTender::where('publish', 1)->get()->count();
		$ad_count = (new static)::where('publish', 1)->get()->count();
		$ads = (new static)::where('field_is_slide', 1)->latest()->take(5)->get();
		return [
			"job_count" => $job_count,
			"tender_count" => $tender_count,
			"ad_count" => $ad_count,
			"ads" => $ads
		];
	}
	
}
