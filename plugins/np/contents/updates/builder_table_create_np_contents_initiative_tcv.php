<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsInitiativeTcv extends Migration
{
    public function up()
    {
        Schema::create('np_contents_initiative_tcv', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->string('initiative_categories_id', 40);
            $table->text('body');
            $table->integer('sov_number')->default(0);
            $table->integer('bw_number')->default(0);
            $table->integer('bv_number')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_initiative_tcv');
    }
}
