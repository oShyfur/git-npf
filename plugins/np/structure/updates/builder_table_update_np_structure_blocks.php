<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureBlocks extends Migration
{
    public function up()
    {
        Schema::table('np_structure_blocks', function($table)
        {
            $table->renameColumn('name', 'title');
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_blocks', function($table)
        {
            $table->renameColumn('title', 'name');
        });
    }
}
