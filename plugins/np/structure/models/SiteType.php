<?php 

namespace Np\Structure\Models;

use Model;

/**
 * Model
 */
class SiteType extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['name'];

    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_site_types';

    /**
     * @var array Validation rules
     */
    public $rules =['name'=>'required'];

    public $hasMany = [
        'sites' => ['Np\Structure\Models\Site']
    ];

    // Define Scope
    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }
}
