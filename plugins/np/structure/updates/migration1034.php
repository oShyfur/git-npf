<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1034 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function($table)
         {
             $table->integer('oisf_office_id')->change();
         });
    }

    public function down()
    {
        Schema::table('np_structure_sites', function($table)
         {
             $table->smallInteger('oisf_office_id')->change();
         });
    }
}