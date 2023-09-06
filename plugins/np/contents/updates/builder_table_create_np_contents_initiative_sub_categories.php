<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsInitiativeSubCategories extends Migration
{
    public function up()
    {
        Schema::create('np_contents_initiative_sub_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('initiative_categories_id', 40);
            $table->integer('sort_order')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_initiative_sub_categories');
    }
}
