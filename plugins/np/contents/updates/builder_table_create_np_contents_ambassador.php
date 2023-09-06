<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsAmbassador extends Migration
{
    public function up()
    {
        Schema::create('np_contents_ambassador', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('block_title');
            $table->text('name');
            $table->text('profile_link');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_ambassador');
    }
}
