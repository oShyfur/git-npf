<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsSubInitiativeSystemAnalysis extends Migration
{
    public function up()
    {
        Schema::create('np_contents_sub_initiative_system_analysis', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->string('initiative_category_id', 40);
            $table->string('initiative_sub_category_id', 40);
            $table->text('system_analysis');
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_sub_initiative_system_analysis');
    }
}
