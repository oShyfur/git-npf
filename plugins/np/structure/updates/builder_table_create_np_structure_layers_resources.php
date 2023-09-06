<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureLayersResources extends Migration
{
    public function up()
    {
        Schema::create('np_structure_layer_resources', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('content_types')->nullable();
            $table->text('taxonomies')->nullable();
            $table->text('blocks')->nullable();
            $table->text('views')->nullable();
            $table->integer('layer_id')->nullable();
            $table->integer('ministry_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_layer_resources');
    }
}
