<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsTesttable extends Migration
{
    public function up()
    {
        Schema::create('np_contents_testtable', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 200);
              $table->contentable();
                $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_testtable');
    }
}
