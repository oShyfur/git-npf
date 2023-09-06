<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsSubInitiativeTcv extends Migration
{
    public function up()
    {
        Schema::create('np_contents_sub_initiative_tcv', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->string('initiative_category_id', 40);
            $table->string('initiative_sub_category_id', 40);
            $table->integer('sc_number')->default(0);
            $table->integer('whsc_number')->default(0);
            $table->integer('ee_number')->default(0);
            $table->integer('sy_number')->default(0);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_sub_initiative_tcv');
    }
}
