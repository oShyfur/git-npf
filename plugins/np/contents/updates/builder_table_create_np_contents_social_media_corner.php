<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsSocialMediaCorner extends Migration
{
    public function up()
    {
        Schema::create('np_contents_social_media_corner', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('field_social_media_url', 255);
            $table->integer('social_link');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_social_media_corner');
    }
}
