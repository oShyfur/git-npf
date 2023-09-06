<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsTest extends Migration
{
    public function up()
    {
        Schema::create('np_contents_test', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 255);
            $table->text('title');
            $table->dateTime('create_at')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_test');
    }
}
