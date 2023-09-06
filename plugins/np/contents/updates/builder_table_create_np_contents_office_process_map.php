<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsOfficeProcessMap extends Migration
{
    public function up()
    {
        Schema::create('np_contents_office_process_map', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->integer('divisionoffice')->nullable();
            $table->integer('upazilla_offices')->nullable();
            $table->integer('offices_union')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_office_process_map');
    }
}
