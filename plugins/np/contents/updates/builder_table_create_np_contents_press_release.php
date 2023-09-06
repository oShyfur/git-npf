<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsPressRelease extends Migration
{
    public function up()
    {
        Schema::create('np_contents_press_release', function($table)
        {
            $table->engine = 'InnoDB';
            $table->text('title');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_press_release');
    }
}
