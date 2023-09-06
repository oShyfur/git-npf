<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsServiceBoxItems extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('np_contents_service_box_items')) {
            
            Schema::create('np_contents_service_box_items', function($table)
            {
                $table->engine = 'InnoDB';
                $table->string('id', 40);
                $table->text('title');
                $table->boolean('is_pin')->default(0);
                $table->string('service_box_category_id', 40);
                $table->text('link')->nullable();
                $table->primary(['id']);
                $table->contentable();
                $table->auditable();
            });
        }
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_service_box_items');
    }
}