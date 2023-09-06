<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsInitiativesAbout extends Migration
{
    public function up()
    {
        Schema::create('np_contents_initiatives_about', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('body');
            $table->string('initiative_categories_id', 40);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_initiatives_about');
    }
}
