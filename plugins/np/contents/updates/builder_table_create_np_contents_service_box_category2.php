<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsServiceBoxCategory2 extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('np_contents_service_box_category')) {
        Schema::create('np_contents_service_box_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
        }
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_service_box_category');
    }
}