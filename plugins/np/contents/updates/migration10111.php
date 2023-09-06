<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use Illuminate\Support\Facades\DB;

class Migration10111 extends Migration
{
    public function up()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        foreach ($tables as $table) {

            if (strpos($table, 'np_contents_') !== false) {

                if (Schema::hasColumn($table, 'body')) {

                    Schema::table($table, function ($table) {

                        $table->longText('body')->change();
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

                if (Schema::hasColumn($table, 'body')) {

                    Schema::table($table, function ($table) {

                        $table->string('body')->change();
                    });
                }
            }
        }
    }
}