<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureTexonomyTypes extends Migration
{
    public function up()
    {
        Schema::create('np_structure_texonomy_types', function ($table) {

            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('is_common')->default(0);
            $table->string('code', 200)->nullable();
            $table->boolean('status')->default(1);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_texonomy_types');
    }
}
