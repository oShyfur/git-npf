<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureContentTypes extends Migration
{
    public function up()
    {
        Schema::table('np_structure_content_types', function($table)
        {
            $table->text('settings')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_content_types', function($table)
        {
            $table->dropColumn('settings');
        });
    }
}
