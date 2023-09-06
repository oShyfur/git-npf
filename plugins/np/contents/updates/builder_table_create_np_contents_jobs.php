<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsJobs extends Migration
{
    public function up()
    {
        Schema::create('np_contents_jobs', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->integer('job_type');
            $table->text('overview')->nullable();
            $table->text('responsibility')->nullable();
            $table->text('qualification')->nullable();
            $table->text('benefit')->nullable();
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_jobs');
    }
}
