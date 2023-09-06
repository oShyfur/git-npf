<?php

namespace Np\Structure\Models;

use Model;

/**
 * Model
 */
class SiteClone extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;

    //jsonable
    public $jsonable = ['resources'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_site_clone';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'source_site_id' => 'required',
        'destination_site_id' => 'required'
    ];

    public $belongsTo = [
        'source_site' => ['Np\Structure\Models\Site'],
        'destination_site' => ['Np\Structure\Models\Site']
    ];
}
