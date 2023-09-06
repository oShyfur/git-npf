<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateNpContentsProject extends Migration
{
    public function up()
    {
        Schema::table('np_contents_project', function($table)
        {
            $table->date('field_project_time_duration_start')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('np_contents_project', function($table)
        {
            $table->date('field_project_time_duration_start')->nullable(false)->change();
        });
    }
}
