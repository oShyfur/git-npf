<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsTenderInfos extends Migration
{
    public function up()
    {
        Schema::create('np_contents_tender_infos', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->text('email');
            $table->text('slogan');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_tender_infos');
    }
}
