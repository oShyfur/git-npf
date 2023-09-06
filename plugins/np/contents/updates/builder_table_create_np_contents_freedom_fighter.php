<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsFreedomFighter extends Migration
{
    public function up()
    {
        Schema::create('np_contents_freedom_fighter', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('field_link');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_freedom_fighter');
    }
}
