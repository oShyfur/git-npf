<?php

namespace Np\Structure\Models;

use Model;
use Np\Structure\Classes\DatabaseConnection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use October\Rain\Support\Facades\Config;

/**
 * Model
 */
class DB extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \Np\Structure\Traits\UsesUuid;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_dbs';
    protected $guarded = ['uuid'];
    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required'
    ];

    // Relations

    public $belongsTo = [
        'cluster' => ['Np\Structure\Models\Cluster']
    ];

    public $hasMany = [
        'sites' => ['Np\Structure\Models\Site']
    ];

    //events

    public function beforeCreate()
    { }
    public function afterCreate()
    {
        $this->createDB();
    }

    public function beforeUpdate()
    { }

    public function createDB()
    {
        $cluster = $this->cluster;
        $connection = DatabaseConnection::setConnection($cluster);
        $schemaName = $this->id;

        //DatabaseConnection::CreateDBAndMigrateCommonTables($connection, $schemaName);
        $connection->statement("CREATE DATABASE IF NOT EXISTS `{$schemaName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        config(['database.connections.onthefly' => [
            'driver' => 'mysql',
            'host' => $cluster->host,
            'username' => $cluster->username,
            'password' => $cluster->password,
            'database' => $schemaName
        ]]);

        Artisan::call('migrate', [
            '--database' => 'onthefly',
            '--path' => 'plugins/np/structure/database/migration/tenant_common',
            '--force' => true
        ]);
    }
}
