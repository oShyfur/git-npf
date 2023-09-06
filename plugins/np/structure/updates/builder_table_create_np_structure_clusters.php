<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureClusters extends Migration
{
    public function up()
    {
        Schema::create('np_structure_clusters', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->text('host');
            $table->text('username');
            $table->text('password');
            $table->integer('sort_order')->unsigned()->default(0);
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
        Schema::dropIfExists('np_structure_clusters');
    }
}
