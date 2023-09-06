<?php namespace Np\Structure\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpStructureContentTypes extends Migration
{
    public function up()
    {
        Schema::create('np_structure_content_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name', 200);
            $table->string('code', 100);
            $table->boolean('is_common')->default(0);
            $table->string('icon', 100)->nullable();
            $table->string('table_name', 200);
            $table->bigInteger('status')->default(1);
            $table->text('settings')->nullable();
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
        Schema::dropIfExists('np_structure_content_types');
    }
}
