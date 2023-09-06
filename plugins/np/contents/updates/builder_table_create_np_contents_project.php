<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsProject extends Migration
{
    public function up()
    {
        Schema::create('np_contents_project', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->text('body')->nullable();
            $table->text('field_project_work_description');
            $table->text('field_project_allotment_taka')->nullable();
            $table->text('field_project_allotment_others')->nullable();
            $table->date('field_project_time_duration_start');
            $table->date('field_project_time_duration_end')->nullable();
            $table->text('field_project_ward')->nullable();
            $table->text('field_project_latest_status')->nullable();
            $table->date('field_latest_update_date')->nullable();
            $table->string('project_implemented_or_proposed', 10)->nullable();
            $table->string('projects', 10)->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_project');
    }
}
