<?php

namespace Np\Structure\Updates;

use Illuminate\Support\Facades\DB;
use Seeder;
use Model;

class SeederCoreData extends Seeder
{
    public function run()
    {

        //truncate np core data from table
        DB::table('system_mail_layouts')->truncate();
        DB::table('system_mail_templates')->truncate();
        DB::table('system_mail_partials')->truncate();
        DB::table('np_structure_geo_districts')->truncate();
        DB::table('np_structure_geo_divisions')->truncate();
        DB::table('np_structure_geo_upazilas')->truncate();
        DB::table('np_structure_geo_unions')->truncate();
        DB::table('np_structure_layers')->truncate();
        DB::table('np_structure_ministries')->truncate();


        // Model::unguard();
        // $path = plugins_path('np/structure/database/seeder/np_core_data.sql');
        // $sql = file_get_contents($path);
        // DB::unprepared($sql);
    }
}