<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsAdc extends Migration
{
    public function up()
    {
        Schema::create('np_contents_adc', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('adc')->nullable();
            $table->text('field_e_directory_nid')->nullable();
            $table->text('field_dc_office_section_nid')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_adc');
    }
}
