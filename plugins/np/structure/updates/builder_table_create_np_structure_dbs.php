<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureDbs extends Migration
{
    public function up()
    {
        Schema::create('np_structure_dbs', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->string('name', 255);
            $table->integer('cluster_id')->unsigned();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->integer('deleted_by')->nullable()->unsigned();
            $table->primary(['id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('np_structure_dbs');
    }
}
