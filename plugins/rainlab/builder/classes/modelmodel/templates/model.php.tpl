<?php namespace {namespace};

{use}
/**
 * Model
 */
class {classname} extends {baseModel}
{
    use \October\Rain\Database\Traits\Validation;
    {dynamicContents}

    /**
     * @var string The database table used by the model.
     */
    public $table = '{table}';

    /**
     * @var array Validation rules
     */
    public $rules ={rules};
}
