<?php

namespace Np\Contents\Updates;

use Illuminate\Support\Facades\DB;
use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1017 extends Migration
{
    public function up()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        foreach ($tables as $table) {

            if (strpos($table, 'np_contents_') !== false) {

                if (Schema::hasColumn($table, 'slug')) {

                    Schema::table($table, function ($table) {

                        $table->string('slug', 512)->change();
                    });
                }
            }
        }
    }

    public function down()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        foreach ($tables as $table) {

            if (strpos($table, 'np_contents_') !== false) {

                if (Schema::hasColumn($table, 'slug')) {

                    Schema::table($table, function ($table) {

                        $table->string('slug', 255)->change();
                    });
                }
            }
        }
    }
}
