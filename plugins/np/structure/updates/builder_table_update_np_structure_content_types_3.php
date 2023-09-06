<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpStructureContentTypes3 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_content_types', function($table)
        {
            $table->integer('frequency')->default(30)->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_content_types', function($table)
        {
            $table->integer('frequency')->default(30)->change();
        });
    }
}
