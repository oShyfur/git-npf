<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteNpContentsPressRelease extends Migration
{
    public function up()
    {
        Schema::dropIfExists('np_contents_press_release');
    }
    
    public function down()
    {
        Schema::create('np_contents_press_release', function($table)
        {
            $table->engine = 'InnoDB';
            $table->text('title');
        });
    }
}
