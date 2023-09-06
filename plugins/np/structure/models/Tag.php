<?php namespace Np\Structure\Models;

use Model;
use Np\Structure\Traits\CacheableEloquent;

/**
 * Model
 */
class Tag extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\SoftDelete;
    use CacheableEloquent;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_tags';

    /**
     * @var array Validation rules
     */
    public $rules = [];
}
