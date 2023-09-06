<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureBlocks4 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_blocks', function($table)
        {
            $table->text('layer_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_blocks', function($table)
        {
            $table->dropColumn('layer_id');
        });
    }
}
