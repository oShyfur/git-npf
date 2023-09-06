<?php

namespace Np\Structure\Models;


/**
 * Model
 */
class LayerResource extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;

    //jsonable
    public $jsonable = ['content_types', 'taxonomies', 'blocks', 'views', 'forms'];

    public $fillable = ['ministry_id', 'layer_id', 'content_types', 'blocks', 'taxonomies', 'views', 'forms'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_layer_resources';

    /**
     * @var array Validation rules
     */
    public $rules = ['layer_id' => 'integer'];

    public $belongsTo = [
        'layer' => ['Np\Structure\Models\Layer'],
        'ministry' => ['Np\Structure\Models\Ministry'],
    ];
}
