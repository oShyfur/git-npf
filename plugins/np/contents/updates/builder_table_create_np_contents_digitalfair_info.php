<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDigitalfairInfo extends Migration
{
    public function up()
    {
        Schema::create('np_contents_digitalfair_info', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('digitalfair_year', 10);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_digitalfair_info');
    }
}
