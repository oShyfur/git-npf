<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureSites6 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->integer('divisional_hierarchy_id')->default(0);
            $table->integer('district_hierarchy_id')->default(0);
            $table->dropColumn('divisional_hierarchy');
            $table->dropColumn('district_hierarchy');
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->dropColumn('divisional_hierarchy_id');
            $table->dropColumn('district_hierarchy_id');
            $table->integer('divisional_hierarchy')->default(0);
            $table->integer('district_hierarchy')->default(0);
        });
    }
}
