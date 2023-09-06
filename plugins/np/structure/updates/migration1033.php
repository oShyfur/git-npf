<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1033 extends Migration
{
   public function up()
    {
        Schema::table('np_structure_sites', function($table)
         {
             $table->smallInteger('oisf_office_id')->nullable();
         });
    }

    public function down()
    {
        Schema::table('np_structure_sites', function($table)
         {
             $table->dropColumn('oisf_office_id');
         });
    }
}