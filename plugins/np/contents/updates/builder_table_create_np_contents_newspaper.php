<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsNewspaper extends Migration
{
    public function up()
    {
        Schema::create('np_contents_newspaper', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('field_editor')->nullable();
            $table->text('field_contact_name')->nullable();
            $table->text('field_contact_designation')->nullable();
            $table->text('field_contact_phone')->nullable();
            $table->text('field_contact_email')->nullable();
            $table->text('field_contact_number')->nullable();
            $table->text('field_address')->nullable();
            $table->text('field_history')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_newspaper');
    }
}
