<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureSites3 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->integer('directorate_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->dropColumn('directorate_id');
        });
    }
}
