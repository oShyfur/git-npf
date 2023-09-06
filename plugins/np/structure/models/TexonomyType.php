<?php namespace Np\Structure\Models;

use Model;

/**
 * Model
 */
class TexonomyType extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\SoftDelete;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'name'
    ];

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_texonomy_types';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required'
    ];
}
