<?php

namespace Np\Contents\Models;

use Illuminate\Support\Facades\DB;
use Model;
use Np\Structure\Models\NPBaseModel;

/**
 * Model
 */
class SiteFeedback extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \Np\Structure\Traits\UsesUuid;
    use \Np\Contents\Traits\SiteContentsTrait;

    public $connection = 'tenant';


    //jsonable
    public $jsonable = ['data'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_site_feedback';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'site_id' => 'required'
    ];

    public function getDataAttribute($value)
    {
        $remove = ['_session_key', '_token', 'form_code', 'domain'];

        $data = json_decode($value, true);

        return json_encode(array_diff_key($data, array_flip($remove)));
    }

    public static function countsSumbissions($formCode)
    {
        return self::select(DB::raw('count(*) as submission_count, form_id'))
            ->where('form_id', $formCode)
            ->groupBy('form_id')
            ->get();
    }
}
