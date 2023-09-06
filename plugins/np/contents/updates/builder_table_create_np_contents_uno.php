<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsUno extends Migration
{
    public function up()
    {
        Schema::create('np_contents_uno', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->text('field_e_directory_nid')->nullable();
            $table->text('field_dc_speech')->nullable();
            $table->date('field_date_of_join')->nullable();
            $table->text('field_telephone')->nullable();
            $table->text('field_mobile')->nullable();
            $table->text('field_email')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_uno');
    }
}
