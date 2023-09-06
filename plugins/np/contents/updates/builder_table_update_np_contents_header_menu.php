<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsHeaderMenu extends Migration
{
    public function up()
    {
        Schema::table('np_contents_header_menu', function($table)
        {
            $table->integer('sort_order');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_header_menu', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
