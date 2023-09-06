<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsMotto extends Migration
{
    public function up()
    {
        Schema::table('np_contents_motto', function($table)
        {
            $table->text('welcome')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_motto', function($table)
        {
            $table->dropColumn('welcome');
        });
    }
}
