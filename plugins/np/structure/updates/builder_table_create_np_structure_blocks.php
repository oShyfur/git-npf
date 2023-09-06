<?php

namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureBlocks extends Migration
{
    public function up()
    {
        Schema::create('np_structure_blocks', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 200);
            $table->string('code', 30)->nullable();
            $table->string('region', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('body')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->dateTime('archive_date')->nullable();
            $table->string('partial_code', 200)->nullable();
            $table->integer('type')->default(1);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('np_structure_blocks');
    }
}
