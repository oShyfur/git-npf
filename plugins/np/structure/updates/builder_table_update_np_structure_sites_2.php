<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureSites2 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->integer('site_type_id')->nullable()->unsigned();
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->dropColumn('site_type_id');
        });
    }
}
