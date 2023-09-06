<?php namespace Np\Contents\Models;

use Model;

/**
 * Model
 */
class TaxonomyType extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\SoftDelete;

    public $connection = 'tenant';

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_texonomy_types';

    /**
     * @var array Validation rules
     */
    public $rules = [];
}
