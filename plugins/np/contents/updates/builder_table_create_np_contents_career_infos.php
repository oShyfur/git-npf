<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsCareerInfos extends Migration
{
    public function up()
    {
        Schema::create('np_contents_career_infos', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('slogan')->nullable();
            $table->text('email')->nullable();
            $table->text('body')->nullable();
            $table->text('opportunity')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_career_infos');
    }
}
