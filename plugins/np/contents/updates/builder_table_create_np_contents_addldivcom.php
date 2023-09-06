<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsAddldivcom extends Migration
{
    public function up()
    {
        Schema::create('np_contents_addldivcom', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('field_body')->nullable();
            $table->text('field_e_directory_nid')->nullable();
            $table->text('field_dc_schedule')->nullable();
            $table->text('field_addldivcom_section_nid')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_addldivcom');
    }
}
