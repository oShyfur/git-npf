<?php

namespace Np\Structure\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Np\Structure\Models\ContentType;

class DatabaseConnection
{
    public static function setConnection($cluster, $db = '')
    {

        config(['database.connections.createnewdb' => [
            'driver' => 'mysql',
            'host' => $cluster->host,
            'username' => $cluster->username,
            'password' => $cluster->password,
            'database' => $db,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci'
        ]]);

        return DB::connection('createnewdb');
    }

    public static function getSql($tableName, $connection)
    {
        if (!Schema::connection('createnewdb')->hasTable($tableName)) {
            $sql = 'SHOW CREATE TABLE ' . $tableName;
            $results = collect(DB::select(DB::raw($sql)));
            return $results->first()->{'Create Table'} . ';';
        }
        return '';
    }

    public static function migrateCT($content_type_ids, $connection, $tmpSqlPath)
    {

        $content_types = ContentType::whereIN('id', $content_type_ids)->orWhere('is_common', 1)->get()->toArray();

        foreach ($content_types as $ct) {
            $tableName = $ct['table_name']::getTableName();
            $contents = self::getSql($tableName, $connection);
            if ($contents)
                file_put_contents($tmpSqlPath, $contents . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    public static function migrateTT($taxonomies, $connection, $tmpSqlPath)
    {
        $taxonomyTable = 'np_contents_texonomy_types';
        $central_taxonomyTable = 'np_structure_texonomy_types';
        $sql = "insert into $taxonomyTable (id,name,description,parent_id,is_common,sort_order)";

        $taxonomies = DB::table($central_taxonomyTable)->whereIN('id', $taxonomies)->orWhere('is_common', 1)->get();

        $values = '';
        foreach ($taxonomies as $taxonomi) {

            $id = $taxonomi->id;

            $row = collect($connection->table($taxonomyTable)->where('id', $id)->get())->first();

            if (!$row) {
                list($id, $name, $description, $parent_id, $is_common, $sort_order) =
                    [$taxonomi->id, $taxonomi->name, $taxonomi->description, $taxonomi->parent_id, $taxonomi->is_common, $taxonomi->sort_order];
                if (!$parent_id)
                    $parent_id = 'null';

                $values .= "($id,'" . $name . "','" . $description . "',$parent_id,$is_common,$sort_order),";
            }
        }
        if ($values) {
            $values = rtrim($values, ',');
            $sql .= ' values ' . $values . ';';
            file_put_contents($tmpSqlPath, $sql . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    public static function migrateBlocks($itemIds, $site_id, $connection, $tmpSqlPath)
    {
        $destinationTable = 'np_contents_blocks';
        $sourceTable = 'np_structure_blocks';
        $sql = "insert into $destinationTable (title,region,sort_order,status,type,partial_code,site_id)";

        $items = DB::table($sourceTable)->whereIN('id', $itemIds)->get();

        $values = '';

        foreach ($items as $item) {

            $partial_code = $item->partial_code;
            $region = $item->region;

            $row = collect($connection->table($destinationTable)->where('region', "$region")->where('partial_code', "$partial_code")->where('site_id', $site_id)->get())->first();

            if (!$row) {
                list($name, $region, $sort_order, $status, $type, $partial_code) =
                    [$item->title, $region, $item->sort_order, $item->status, 1, $item->partial_code,];
                $values .= "('" . $name . "','" . $region . "',$sort_order,$status,$type,'" . $partial_code . "',$site_id),";
            }
        }
        if ($values) {
            $values = rtrim($values, ',');
            $sql .= ' values ' . $values . ';';
            file_put_contents($tmpSqlPath, $sql . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}
