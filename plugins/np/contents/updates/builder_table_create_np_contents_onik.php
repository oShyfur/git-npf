<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsOnik extends Migration
{
    public function up()
    {
        Schema::create('np_contents_onik', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('do')->nullable();
            $table->text('ao')->nullable();
            $table->text('aa')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_onik');
    }
}
