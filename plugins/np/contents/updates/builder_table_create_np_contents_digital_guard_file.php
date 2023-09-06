<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDigitalGuardFile extends Migration
{
    public function up()
    {
        Schema::create('np_contents_digital_guard_file', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->text('field_digital_guard')->nullable();
            $table->text('field_memo_no')->nullable();
            $table->string('field_digital_guard_file_nid', 40)->nullable();
            $table->string('digital_guard_file', 10)->nullable();
            $table->string('section_dcoffice', 10)->nullable();
            $table->string('section_divcom', 10)->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_digital_guard_file');
    }
}
