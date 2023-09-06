<?php

namespace Np\Structure\Models;

use Np\Structure\Models\Ministry;

/**
 * Model
 */
class Directorate extends NPBaseModel
{
    public $incrementing = false;

    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\Validation;
    //Translatable
    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['name'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;



    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_directorates';

    /**
     * @var array Validation rules
     */
    public $rules = ['name' => 'required', 'id' => 'required|integer|unique:np_structure_directorates'];

    // Define the relationship to the Ministry model
    public $belongsTo = [
        'ministry' => ['Np\Structure\Models\Ministry'],
    ];

    // Define an accessor method to concatenate name and id
    public function getNameWithDomainAttribute()
    {
        return $this->name . ' [' . $this->subdomain . ']';
    }
}
