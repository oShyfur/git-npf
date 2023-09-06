<?php namespace Np\Structure\Models;

use Model;

/**
 * Model
 */
class Cluster extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Encryptable;

    protected $dates = ['deleted_at'];
    protected $encryptable = ['host', 'username', 'password'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_clusters';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'host' => 'required',
        'username' => 'required',
        'password' => 'required'
    ];

    public $hasMany = [
        'dbs' => ['Np\Structure\Models\DB']
    ];
}
