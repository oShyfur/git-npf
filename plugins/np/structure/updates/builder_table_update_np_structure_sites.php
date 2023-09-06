<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureSites extends Migration
{
    public function up()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->text('site_title_line1');
            $table->text('site_title_line2');
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_sites', function($table)
        {
            $table->dropColumn('site_title_line1');
            $table->dropColumn('site_title_line2');
        });
    }
}
