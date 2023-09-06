<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsFreedomFighter extends Migration
{
    public function up()
    {
        Schema::table('np_contents_freedom_fighter', function($table)
        {
            $table->text('title');
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_freedom_fighter', function($table)
        {
            $table->dropColumn('title');
        });
    }
}
