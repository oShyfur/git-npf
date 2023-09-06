<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureSiteClone extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('np_structure_site_clone')) {
            
            Schema::create('np_structure_site_clone', function($table)
            {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->integer('source_site_id');
                $table->integer('destination_site_id');
                $table->text('resources')->nullable();
                $table->dateTime('cloned_at');
            });
        }
    }
    
    public function down()
    {
        Schema::dropIfExists('np_structure_site_clone');
    }
}
