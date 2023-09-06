<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsHeaderMenu2 extends Migration
{
    public function up()
    {
        Schema::table('np_contents_header_menu', function($table)
        {
            $table->string('parent_id', 40)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_header_menu', function($table)
        {
            $table->dropColumn('parent_id');
        });
    }
}
