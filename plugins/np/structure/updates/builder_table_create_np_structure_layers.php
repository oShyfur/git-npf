<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureLayers extends Migration
{
    public function up()
    {
        Schema::create('np_structure_layers', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('code', 50);
            $table->integer('parent_id')->nullable()->unsigned();
            $table->boolean('status')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->integer('deleted_by')->nullable()->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_layers');
    }
}
