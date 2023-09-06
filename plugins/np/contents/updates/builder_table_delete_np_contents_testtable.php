<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteNpContentsTesttable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('np_contents_testtable');
    }
    
    public function down()
    {
        Schema::create('np_contents_testtable', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 200);
            $table->string('slug', 512);
            $table->dateTime('publish_date')->nullable();
            $table->dateTime('archive_date')->nullable();
            $table->boolean('publish')->default(1);
            $table->boolean('is_right_side_bar')->default(1);
            $table->integer('site_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->integer('deleted_by')->nullable()->unsigned();
        });
    }
}
