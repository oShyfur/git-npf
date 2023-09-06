<?php namespace Np\Contents\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateNpContentsDigitalfairEduEmp extends Migration
{
    public function up()
    {
        Schema::create('np_contents_digitalfair_edu_emp', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 40);
            $table->text('title');
            $table->string('digitalfair_year', 10);
            $table->string('digitalfair_edu_emp_category', 10);
            $table->primary(['id']);
            $table->contentable();
            $table->auditable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('np_contents_digitalfair_edu_emp');
    }
}
