<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsContactInfos extends Migration
{
    public function up()
    {
        Schema::create('np_contents_contact_infos', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body');
            $table->text('phone');
            $table->text('fax');
            $table->text('email');
            $table->text('google_map_link');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_contact_infos');
    }
}
