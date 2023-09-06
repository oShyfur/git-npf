<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureBlocks2 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_blocks', function($table)
        {
            $table->string('predefined_template_code', 100)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_blocks', function($table)
        {
            $table->dropColumn('predefined_template_code');
        });
    }
}
