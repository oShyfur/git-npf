<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1029 extends Migration
{
    public function up()
    {
         Schema::table('np_structure_texonomy_types', function($table)
         {
             $table->integer('old_id')->nullable()->unsigned();
         });
    }

    public function down()
    {
       Schema::table('np_structure_texonomy_types', function ($table) {
           
            $table->dropColumn(['old_id']);
            
        });
    }
}