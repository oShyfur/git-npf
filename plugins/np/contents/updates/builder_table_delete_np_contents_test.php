<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteNpContentsTest extends Migration
{
    public function up()
    {
        Schema::dropIfExists('np_contents_test');
    }
    
    public function down()
    {
        Schema::create('np_contents_test', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 255);
            $table->text('title');
            $table->dateTime('create_at')->nullable();
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
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
}
