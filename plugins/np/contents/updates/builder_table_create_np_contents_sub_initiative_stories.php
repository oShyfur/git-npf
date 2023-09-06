<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsSubInitiativeStories extends Migration
{
    public function up()
    {
        Schema::create('np_contents_sub_initiative_stories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->string('initiative_category_id', 40);
            $table->string('initiative_sub_category_id', 40);
            $table->text('title');
            $table->text('body');
            $table->text('relevant_story')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_sub_initiative_stories');
    }
}
