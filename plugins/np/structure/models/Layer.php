<?php

namespace Np\Structure\Models;

use Model;

/**
 * Model
 */
class Layer extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    public $implement = [
        'RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'name'
    ];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_layers';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'code' => 'required',
    ];

    public $hasMany = [
        'sites' => ['Np\Structure\Models\Site']
    ];

    // Define Scope
    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }
    
    public function scopeOnlyParent($query)
    {
        $query->where('parent_id', null);
    }
}
