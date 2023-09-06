<?php

namespace Np\Structure\Models;

use Model;
use October\Rain\Support\Facades\Schema;
use October\Rain\Support\Str;
use RainLab\Builder\Classes\ComponentHelper;

/**
 * Model
 */
class ContentType extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;

    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];
    protected $jsonable = ['settings'];

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'name',
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_content_types';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:np_structure_content_types',
    ];

    public function filterFields($fields, $context = null)
    {
        //traceLog($fields->settings);
        if($this->table_name){
        
            $tableName = $this->table_name::getTableName();
            $columns = Schema::getColumnListing($tableName);
            
            $fields->settings->comment = implode('<br/>',$columns);
        }
        
    }


    public function getFieldDataTableOptions()
    {
        return [
            'v1'=>'V2'
        ];
    }
    public function getTableNameOptions()
    {
        $list = [];
        $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
        $tables = $schemaManager->listTableNames();
        $prefix = 'np_contents_';
        $tables =  array_filter($tables, function ($item) use ($prefix) {
            return Str::startsWith($item, $prefix);
        });

        foreach ($tables as $table) {
            $list[$table] = $table;
        }

        return $list;
    }

    public function getModelList()
    {
        $models = collect(ComponentHelper::instance()->listGlobalModels())->filter(function ($value, $key) {
            return strpos($key, 'Np\Contents\Models') !== false;
        });
        return $models;
    }
}
