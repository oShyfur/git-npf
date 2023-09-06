<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsPhysicalLocations extends Migration
{
    public function up()
    {
        Schema::create('np_contents_physical_locations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 100);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_physical_locations');
    }
}
