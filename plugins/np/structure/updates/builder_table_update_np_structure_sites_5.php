<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureSites5 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->integer('divisional_hierarchy')->default(0);
            $table->integer('district_hierarchy')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->dropColumn('divisional_hierarchy');
            $table->dropColumn('district_hierarchy');
        });
    }
}
