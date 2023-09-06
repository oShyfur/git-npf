<?php namespace Np\Structure\Models;

use Model;

/**
 * Model
 */
class Domain extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_domains';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'fqdn' => 'required'
    ];

    public $belongsTo = [
        'site' => ['Np\Structure\Models\Site'],
    ];
}
