<?php namespace RainLab\Translate\Models;

use Model;

/**
 * Attribute Model
 */
class Attribute extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'rainlab_translate_attributes';

    protected $guarded=[];

    public $morphTo = [
        'model' => []
    ];

    public $timestamps = false;
}
