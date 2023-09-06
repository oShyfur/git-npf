<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureSiteResources extends Migration
{
    public function up()
    {
        Schema::create('np_structure_site_resources', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('content_types')->nullable();
            $table->text('taxonomies')->nullable();
            $table->text('blocks')->nullable();
            $table->text('views')->nullable();
            $table->integer('site_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_structure_site_resources');
    }
}
