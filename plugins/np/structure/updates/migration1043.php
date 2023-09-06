<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class Migration1043 extends Migration
{
    public function up()
    {
        Schema::table('np_structure_site_resources', function($table)
        {
            $table->text('forms')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('np_structure_site_resources', function($table)
        {
            $table->dropColumn('forms');
        });
    }
}